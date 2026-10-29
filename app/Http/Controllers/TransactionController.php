<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockMovement;
use Illuminate\Http\Request;
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
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('pages.pos.index', compact('products', 'categories', 'brands', 'customers'));
    }

    // Memproses transaksi dari kasir
    public function store(Request $request)
    {
        try {
            $request->validate([
                'cart_data' => ['required', 'string', 'max:20000'],
                'pay_amount' => ['required', 'integer', 'min:0', 'max:2147483647'],
                'discount_amount' => ['sometimes', 'integer', 'min:0', 'max:2147483647'],
                'customer_type' => ['sometimes', 'in:regular,reseller,internal,other'],
                'payment_method' => ['sometimes', 'in:cash,transfer,qris,debit,credit'],
                'customer_name' => ['nullable', 'required_if:payment_method,credit', 'string', 'max:255'],
                'customer_phone' => ['nullable', 'string', 'max:30'],
                'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
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
                'items.*.discount' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
            ], [
                'items.*.id.distinct' => 'Produk yang sama muncul lebih dari sekali di keranjang.',
            ])->validate();

            $items = collect($cartItems)->map(fn (array $item) => [
                'id' => (int) $item['id'],
                'quantity' => (int) $item['qty'],
                'discount' => (int) ($item['discount'] ?? 0),
            ])->values();
            $payAmount = (int) $request->input('pay_amount');
            $orderDiscount = (int) $request->input('discount_amount', 0);

            $customerType = $request->input('customer_type', 'regular');
            $paymentMethod = $request->input('payment_method', 'cash');
            $customerName = $request->input('customer_name');
            $customerPhone = $request->input('customer_phone');
            $customerId = $request->input('customer_id');
            $transaction = DB::transaction(function () use ($items, $payAmount, $orderDiscount, $customerType, $paymentMethod, $customerName, $customerPhone, $customerId, $request) {
                $customer = $customerId
                    ? Customer::query()->where('is_active', true)->find($customerId)
                    : null;

                if ($customerId && ! $customer) {
                    throw ValidationException::withMessages([
                        'customer_id' => 'Customer yang dipilih sudah tidak aktif.',
                    ]);
                }

                if ($customer) {
                    $customerType = $customer->customer_type;
                    $customerName = $customer->name;
                    $customerPhone = $customer->phone;
                }

                $products = Product::query()
                    ->whereIn('id', $items->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotalAmount = 0;
                $pricedItems = [];

                foreach ($items as $item) {
                    $product = $products->get($item['id']);

                    if (! $product) {
                        throw ValidationException::withMessages([
                            'cart_data' => 'Salah satu produk sudah tidak tersedia. Silakan periksa keranjang.',
                        ]);
                    }

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'cart_data' => 'Stok produk '.$product->name.' tidak mencukupi.',
                        ]);
                    }

                    $grossSubtotal = (int) $product->selling_price * $item['quantity'];

                    if ($grossSubtotal > 2147483647 || $item['discount'] > $grossSubtotal) {
                        throw ValidationException::withMessages([
                            'cart_data' => 'Diskon produk tidak valid atau melebihi nilai produk pada keranjang.',
                        ]);
                    }

                    $lineSubtotal = $grossSubtotal - $item['discount'];
                    if ($lineSubtotal > 2147483647 - $subtotalAmount) {
                        throw ValidationException::withMessages([
                            'cart_data' => 'Subtotal keranjang melebihi batas nominal transaksi.',
                        ]);
                    }
                    $subtotalAmount += $lineSubtotal;

                    $pricedItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'discount' => $item['discount'],
                        'line_subtotal' => $lineSubtotal,
                    ];
                }

                if ($orderDiscount > $subtotalAmount) {
                    throw ValidationException::withMessages([
                        'discount_amount' => 'Diskon seluruh transaksi tidak boleh melebihi subtotal keranjang.',
                    ]);
                }

                $totalAmount = $subtotalAmount - $orderDiscount;

                if ($paymentMethod !== 'credit' && $payAmount < $totalAmount) {
                    throw ValidationException::withMessages([
                        'pay_amount' => 'Uang diterima harus sama atau lebih besar dari total belanja.',
                    ]);
                }

                $transaction = Transaction::create([
                    'subtotal_amount' => $subtotalAmount,
                    'discount_amount' => $orderDiscount,
                    'total_amount' => $totalAmount,
                    'pay_amount' => $payAmount,
                    'customer_type' => $customerType,
                    'payment_method' => $paymentMethod,
                    'customer_name' => $customerName,
                    'customer_phone' => $customerPhone,
                    'customer_id' => $customer?->id,
                ]);

                $discountAllocations = [];
                $allocatedDiscount = 0;

                if ($subtotalAmount > 0 && $orderDiscount > 0) {
                    foreach ($pricedItems as $index => $item) {
                        $share = intdiv($orderDiscount * $item['line_subtotal'], $subtotalAmount);
                        $discountAllocations[$index] = $share;
                        $allocatedDiscount += $share;
                    }

                    $remainingDiscount = $orderDiscount - $allocatedDiscount;
                    foreach ($pricedItems as $index => $item) {
                        if ($remainingDiscount === 0) {
                            break;
                        }

                        if ($item['line_subtotal'] > $discountAllocations[$index]) {
                            $discountAllocations[$index]++;
                            $remainingDiscount--;
                        }
                    }
                }

                foreach ($pricedItems as $index => $item) {
                    $product = $item['product'];
                    $quantity = $item['quantity'];
                    $lineSubtotal = $item['line_subtotal'];
                    $discountShare = $discountAllocations[$index] ?? 0;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->selling_price,
                        'discount_amount' => $item['discount'],
                        'hpp' => $product->purchase_price,
                        'subtotal' => $lineSubtotal - $discountShare,
                    ]);

                    $product->decrement('stock', $quantity);
                    $product->refresh();
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => $request->user()->id,
                        'movement_type' => 'sale_out',
                        'quantity_out' => $quantity,
                        'balance_after' => $product->stock,
                        'source_type' => Transaction::class,
                        'source_id' => $transaction->id,
                        'reference' => 'TRX-'.$transaction->id,
                        'notes' => 'Penjualan '.$customerType.'.',
                    ]);
                }

                $outstandingAmount = max(0, $totalAmount - $payAmount);
                if ($paymentMethod === 'credit' && $outstandingAmount > 0) {
                    Debt::create([
                        'direction' => 'receivable',
                        'party_type' => 'customer',
                        'party_name' => $customerName,
                        'reference' => 'TRX-'.$transaction->id,
                        'amount' => $outstandingAmount,
                        'paid_amount' => 0,
                        'status' => 'open',
                        'notes' => 'Piutang otomatis dari transaksi POS.',
                        'created_by' => $request->user()->id,
                    ]);
                }

                return $transaction;
            });

            return redirect()->route('pos.index')
                ->with('success', 'Kembalian: Rp '.number_format($payAmount - $transaction->total_amount, 0, ',', '.'))
                ->with('print_id', $transaction->id);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Transaksi gagal diproses. Silakan coba kembali.');
        }
    }
}
