<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->latest('purchase_date')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $purchases = $query->paginate(15)->withQueryString();

        return view('pages.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::with('category')->orderBy('name')->get();

        return view('pages.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source' => ['required', 'in:supplier,self_purchase'],
            'status' => ['sometimes', 'in:draft,received'],
            'supplier_id' => ['nullable', 'required_if:source,supplier', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'items.*.unit_cost' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {
                $products = Product::query()
                    ->whereIn('id', collect($validated['items'])->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $total = 0;

                foreach ($validated['items'] as $item) {
                    $product = $products->get((int) $item['product_id']);
                    $subtotal = (int) $item['quantity'] * (int) $item['unit_cost'];

                    if (! $product || $subtotal > 2147483647 || $total > 2147483647 - $subtotal) {
                        throw ValidationException::withMessages(['items' => 'Data pembelian tidak valid atau total terlalu besar.']);
                    }

                    $total += $subtotal;
                }

                $purchase = Purchase::create([
                    'supplier_id' => $validated['source'] === 'supplier' ? $validated['supplier_id'] : null,
                    'created_by' => $request->user()->id,
                    'source' => $validated['source'],
                    'status' => $validated['status'] ?? 'received',
                    'purchase_date' => $validated['purchase_date'],
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'total_amount' => $total,
                    'notes' => $validated['notes'] ?? null,
                    'received_at' => ($validated['status'] ?? 'received') === 'received' ? now() : null,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = $products->get((int) $item['product_id']);
                    $quantity = (int) $item['quantity'];
                    $subtotal = $quantity * (int) $item['unit_cost'];
                    $purchase->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_cost' => $item['unit_cost'],
                        'subtotal' => $subtotal,
                    ]);
                    if ($purchase->status === 'received') {
                        $product->increment('stock', $quantity);
                        $product->refresh();
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $request->user()->id,
                            'movement_type' => 'purchase_in',
                            'quantity_in' => $quantity,
                            'balance_after' => $product->stock,
                            'source_type' => Purchase::class,
                            'source_id' => $purchase->id,
                            'reference' => $purchase->invoice_number,
                            'notes' => $validated['source'] === 'supplier' ? 'Pembelian dari supplier.' : 'Belanja mandiri.',
                        ]);
                    }
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Pembelian gagal disimpan. Tidak ada perubahan stok.');
        }

        $message = ($validated['status'] ?? 'received') === 'draft'
            ? 'Draft pembelian berhasil disimpan.'
            : 'Pembelian diterima dan stok berhasil ditambahkan.';

        return redirect()->route('purchases.index')->with('success', $message);
    }

    public function receive(Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {
                $purchase = Purchase::with('items')->lockForUpdate()->findOrFail($purchase->id);

                if ($purchase->status !== 'draft') {
                    throw ValidationException::withMessages(['purchase' => 'Hanya pembelian draft yang dapat diterima.']);
                }

                $products = Product::whereIn('id', $purchase->items->pluck('product_id'))
                    ->lockForUpdate()->get()->keyBy('id');

                foreach ($purchase->items as $item) {
                    $product = $products->get($item->product_id);
                    if (! $product) {
                        throw ValidationException::withMessages(['purchase' => 'Produk pembelian tidak ditemukan.']);
                    }
                    $product->increment('stock', $item->quantity);
                    $product->refresh();
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'movement_type' => 'purchase_in',
                        'quantity_in' => $item->quantity,
                        'balance_after' => $product->stock,
                        'source_type' => Purchase::class,
                        'source_id' => $purchase->id,
                        'reference' => $purchase->invoice_number,
                        'notes' => 'Penerimaan pembelian.',
                    ]);
                }

                $purchase->update(['status' => 'received', 'received_at' => now()]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Pembelian tidak dapat diterima.');
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian diterima dan stok berhasil ditambahkan.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'creator', 'items.product']);

        return view('pages.purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {
                $purchase = Purchase::query()->with('items')->lockForUpdate()->findOrFail($purchase->id);

                if ($purchase->status !== 'received') {
                    throw ValidationException::withMessages(['purchase' => 'Pembelian ini sudah dibatalkan.']);
                }

                $products = Product::query()
                    ->whereIn('id', $purchase->items->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($purchase->items as $item) {
                    $product = $products->get($item->product_id);
                    if (! $product || $product->stock < $item->quantity) {
                        throw ValidationException::withMessages(['purchase' => 'Stok saat ini tidak mencukupi untuk membatalkan pembelian.']);
                    }
                }

                foreach ($purchase->items as $item) {
                    $product = $products->get($item->product_id);
                    $product->decrement('stock', $item->quantity);
                    $product->refresh();
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'movement_type' => 'purchase_cancel',
                        'quantity_out' => $item->quantity,
                        'balance_after' => $product->stock,
                        'source_type' => Purchase::class,
                        'source_id' => $purchase->id,
                        'reference' => $purchase->invoice_number,
                        'notes' => 'Pembatalan pembelian.',
                    ]);
                }

                $purchase->update(['status' => 'cancelled']);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Pembelian tidak dapat dibatalkan.');
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian dibatalkan dan stok dikurangi.');
    }
}
