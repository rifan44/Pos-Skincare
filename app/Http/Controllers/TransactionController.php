<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransactionController extends Controller
{
    // Menampilkan halaman antarmuka kasir
    public function index()
    {
        $products = Product::with(['category', 'brand'])
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
        $categories = Category::where('category_type', 'jenis')->orderBy('name')->get();
        $brands = Category::where('category_type', 'merek')->orderBy('name')->get();

        return view('pages.pos.index', compact('products', 'categories', 'brands'));
    }

    // Memproses transaksi dari kasir
    public function store(Request $request)
    {
        try {
            $request->validate([
                'cart_data' => ['required', 'string', 'max:20000'],
                'pay_amount' => ['required', 'integer', 'min:0'],
            ]);

            try {
                $cartItems = json_decode($request->input('cart_data'), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw ValidationException::withMessages([
                    'cart_data' => 'Data keranjang tidak valid. Silakan susun ulang keranjang.',
                ]);
            }

            Validator::make(['items' => $cartItems], [
                'items' => ['required', 'array', 'min:1'],
                'items.*.id' => ['required', 'integer', 'min:1', 'distinct'],
                'items.*.qty' => ['required', 'integer', 'min:1'],
            ], [
                'items.*.id.distinct' => 'Produk yang sama muncul lebih dari sekali di keranjang.',
            ])->validate();

            $items = collect($cartItems)->map(fn (array $item) => [
                'id' => (int) $item['id'],
                'quantity' => (int) $item['qty'],
            ])->values();
            $payAmount = (int) $request->input('pay_amount');

            $transaction = DB::transaction(function () use ($items, $payAmount) {
                $products = Product::query()
                    ->whereIn('id', $items->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $totalAmount = 0;

                foreach ($items as $item) {
                    $product = $products->get($item['id']);

                    if (! $product) {
                        throw ValidationException::withMessages([
                            'cart_data' => 'Salah satu produk sudah tidak tersedia. Silakan periksa keranjang.',
                        ]);
                    }

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'cart_data' => 'Stok produk ' . $product->name . ' tidak mencukupi.',
                        ]);
                    }

                    $totalAmount += (int) $product->selling_price * $item['quantity'];
                }

                if ($payAmount < $totalAmount) {
                    throw ValidationException::withMessages([
                        'pay_amount' => 'Uang diterima harus sama atau lebih besar dari total belanja.',
                    ]);
                }

                $transaction = Transaction::create([
                    'total_amount' => $totalAmount,
                    'pay_amount' => $payAmount,
                ]);

                foreach ($items as $item) {
                    $product = $products->get($item['id']);
                    $subtotal = (int) $product->selling_price * $item['quantity'];

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->selling_price,
                        'hpp' => $product->purchase_price,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $item['quantity']);
                }

                return $transaction;
            });

            return redirect()->route('pos.index')
                ->with('success', 'Kembalian: Rp ' . number_format($payAmount - $transaction->total_amount, 0, ',', '.'))
                ->with('print_id', $transaction->id);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Transaksi gagal diproses. Silakan coba kembali.');
        }
    }
}
