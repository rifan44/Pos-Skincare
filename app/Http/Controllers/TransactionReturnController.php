<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionReturn;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class TransactionReturnController extends Controller
{
    private const RETURN_WINDOW_DAYS = 7;

    public function index(Transaction $transaction): View
    {
        $transaction->load(['details.product', 'returns.details.transactionDetail.product']);
        $returnedQuantities = [];

        foreach ($transaction->returns as $transactionReturn) {
            foreach ($transactionReturn->details as $detail) {
                $returnedQuantities[$detail->transaction_detail_id] =
                    ($returnedQuantities[$detail->transaction_detail_id] ?? 0) + (int) $detail->quantity;
            }
        }

        $returnWindowEndsAt = $transaction->created_at
            ->copy()
            ->addDays(self::RETURN_WINDOW_DAYS);
        $returnWindowOpen = now()->lte($returnWindowEndsAt);

        return view('pages.history.returns', compact(
            'transaction', 'returnedQuantities', 'returnWindowEndsAt', 'returnWindowOpen'
        ));
    }

    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'refund_method' => ['required', 'in:cash,transfer'],
            'reference' => ['nullable', 'required_if:refund_method,transfer', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
            'items.*.restocked_quantity' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $transactionReturn = DB::transaction(function () use ($request, $transaction, $validated) {
                $sale = Transaction::query()
                    ->whereKey($transaction->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $returnWindowEndsAt = $sale->created_at
                    ->copy()
                    ->addDays(self::RETURN_WINDOW_DAYS);

                if (now()->gt($returnWindowEndsAt)) {
                    throw ValidationException::withMessages([
                        'items' => 'Masa retur telah berakhir. Retur hanya dapat diproses dalam 7 hari sejak transaksi.',
                    ]);
                }

                $saleDetails = TransactionDetail::query()
                    ->where('transaction_id', $sale->id)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($saleDetails->isEmpty()
                    || (int) $saleDetails->sum('subtotal') !== (int) $sale->total_amount
                    || (int) $sale->subtotal_amount - (int) $sale->discount_amount !== (int) $sale->total_amount) {
                    throw ValidationException::withMessages([
                        'items' => 'Rincian struk tidak cocok dengan total transaksi. Retur otomatis ditolak agar nominal refund tetap akurat.',
                    ]);
                }

                foreach ($saleDetails as $detail) {
                    $grossAmount = (int) $detail->price * (int) $detail->quantity;
                    $itemSubtotal = $grossAmount - (int) $detail->discount_amount;

                    if ((int) $detail->quantity < 1
                        || (int) $detail->discount_amount < 0
                        || (int) $detail->discount_amount > $grossAmount
                        || (int) $detail->subtotal < 0
                        || (int) $detail->subtotal > $itemSubtotal) {
                        throw ValidationException::withMessages([
                            'items' => 'Rincian harga struk tidak konsisten. Retur otomatis ditolak agar nominal refund tetap akurat.',
                        ]);
                    }
                }

                $previousReturns = TransactionReturn::query()
                    ->with('details')
                    ->where('transaction_id', $sale->id)
                    ->lockForUpdate()
                    ->get();
                $returnedQuantities = [];
                $returnedAmounts = [];

                foreach ($previousReturns as $previousReturn) {
                    foreach ($previousReturn->details as $returnedDetail) {
                        $detailId = $returnedDetail->transaction_detail_id;
                        $returnedQuantities[$detailId] = ($returnedQuantities[$detailId] ?? 0) + (int) $returnedDetail->quantity;
                        $returnedAmounts[$detailId] = ($returnedAmounts[$detailId] ?? 0) + (int) $returnedDetail->refund_amount;
                    }
                }

                $refundLines = [];
                $refundTotal = 0;

                foreach ($validated['items'] as $detailId => $item) {
                    $detailId = filter_var((string) $detailId, FILTER_VALIDATE_INT);

                    if ($detailId === false || ! $saleDetails->has($detailId)) {
                        throw ValidationException::withMessages([
                            'items' => 'Rincian barang retur tidak termasuk dalam transaksi ini.',
                        ]);
                    }

                    $quantity = (int) $item['quantity'];
                    $restockedQuantity = (int) $item['restocked_quantity'];

                    if ($restockedQuantity > $quantity) {
                        throw ValidationException::withMessages([
                            'items.'.$detailId.'.restocked_quantity' => 'Jumlah yang masuk kembali ke stok tidak boleh melebihi jumlah barang retur.',
                        ]);
                    }

                    if ($quantity === 0) {
                        if ($restockedQuantity !== 0) {
                            throw ValidationException::withMessages([
                                'items.'.$detailId.'.restocked_quantity' => 'Isi jumlah barang retur sebelum menentukan jumlah stok kembali.',
                            ]);
                        }

                        continue;
                    }

                    $saleDetail = $saleDetails->get($detailId);
                    $remainingQuantity = (int) $saleDetail->quantity - ($returnedQuantities[$detailId] ?? 0);

                    if ($quantity > $remainingQuantity) {
                        throw ValidationException::withMessages([
                            'items.'.$detailId.'.quantity' => 'Jumlah retur melebihi sisa barang yang belum diretur.',
                        ]);
                    }

                    $returnedQuantityBefore = $returnedQuantities[$detailId] ?? 0;
                    $unitRefund = intdiv((int) $saleDetail->subtotal, (int) $saleDetail->quantity);
                    $extraRupiahUnits = (int) $saleDetail->subtotal % (int) $saleDetail->quantity;
                    $refundAmount = ($unitRefund * $quantity)
                        + max(0, min($returnedQuantityBefore + $quantity, $extraRupiahUnits) - min($returnedQuantityBefore, $extraRupiahUnits));

                    if (($returnedAmounts[$detailId] ?? 0) + $refundAmount > (int) $saleDetail->subtotal) {
                        throw ValidationException::withMessages([
                            'items.'.$detailId.'.quantity' => 'Nominal retur melebihi nilai barang pada struk.',
                        ]);
                    }

                    $refundLines[] = [
                        'sale_detail' => $saleDetail,
                        'quantity' => $quantity,
                        'restocked_quantity' => $restockedQuantity,
                        'refund_amount' => $refundAmount,
                    ];
                    $refundTotal += $refundAmount;
                }

                if ($refundTotal <= 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Pilih setidaknya satu barang dengan jumlah retur lebih dari nol.',
                    ]);
                }

                $alreadyRefunded = (int) $previousReturns->sum('total_amount');

                if ($refundTotal > (int) $sale->total_amount - $alreadyRefunded) {
                    throw ValidationException::withMessages([
                        'items' => 'Nominal refund melebihi sisa nilai transaksi yang dapat dikembalikan.',
                    ]);
                }

                $refund = TransactionReturn::create([
                    'transaction_id' => $sale->id,
                    'processed_by' => $request->user()->id,
                    'processed_by_name' => $request->user()->name,
                    'reason' => $validated['reason'],
                    'total_amount' => $refundTotal,
                    'refund_method' => $validated['refund_method'],
                    'reference' => $validated['reference'] ?? null,
                ]);

                foreach ($refundLines as $line) {
                    $saleDetail = $line['sale_detail'];

                    $refund->details()->create([
                        'transaction_detail_id' => $saleDetail->id,
                        'quantity' => $line['quantity'],
                        'restocked_quantity' => $line['restocked_quantity'],
                        'unit_price_snapshot' => $saleDetail->price,
                        'hpp_snapshot' => $saleDetail->hpp,
                        'refund_amount' => $line['refund_amount'],
                    ]);

                    if ($line['restocked_quantity'] > 0) {
                        $product = Product::withTrashed()
                            ->whereKey($saleDetail->product_id)
                            ->lockForUpdate()
                            ->first();

                        if (! $product) {
                            throw ValidationException::withMessages([
                                'items' => 'Data produk retur tidak tersedia sehingga stok tidak dapat diperbarui.',
                            ]);
                        }

                        if ($product->trashed()) {
                            throw ValidationException::withMessages([
                                'items' => 'Produk yang sudah dihapus tidak dapat menerima stok retur. Pulihkan produk atau retur tanpa memasukkan kembali ke stok.',
                            ]);
                        }

                        $product->increment('stock', $line['restocked_quantity']);
                        $product->refresh();
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $request->user()->id,
                            'movement_type' => 'return_in',
                            'quantity_in' => $line['restocked_quantity'],
                            'balance_after' => $product->stock,
                            'source_type' => TransactionReturn::class,
                            'source_id' => $refund->id,
                            'reference' => 'RET-'.$refund->id,
                            'notes' => 'Stok kembali dari retur penjualan.',
                        ]);
                    }
                }

                return $refund;
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Retur tidak dapat diproses. Tidak ada perubahan transaksi atau stok yang disimpan.');
        }

        return redirect()
            ->route('history.returns.index', $transaction)
            ->with('success', 'Refund sebesar Rp '.number_format($transactionReturn->total_amount, 0, ',', '.').' berhasil dicatat.');
    }

    public function print(TransactionReturn $transactionReturn): View
    {
        $transactionReturn->load([
            'transaction',
            'details.transactionDetail.product',
        ]);
        $setting = Setting::first();

        return view('pages.history.return-print', compact('transactionReturn', 'setting'));
    }
}
