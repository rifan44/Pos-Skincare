<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class StockAdjustmentController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('pages.stock-adjustments.create', [
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku', 'stock']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'direction' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated): void {
                $product = Product::query()
                    ->whereKey($validated['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $quantity = (int) $validated['quantity'];

                if ($validated['direction'] === 'out' && $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Jumlah pengurangan melebihi stok tersedia.',
                    ]);
                }

                $movementType = $validated['direction'] === 'in' ? 'adjustment_in' : 'adjustment_out';
                $validated['direction'] === 'in'
                    ? $product->increment('stock', $quantity)
                    : $product->decrement('stock', $quantity);
                $product->refresh();

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'movement_type' => $movementType,
                    'quantity_in' => $validated['direction'] === 'in' ? $quantity : 0,
                    'quantity_out' => $validated['direction'] === 'out' ? $quantity : 0,
                    'balance_after' => $product->stock,
                    'reference' => 'ADJ-'.$product->id.'-'.now()->format('YmdHis'),
                    'notes' => $validated['reason'],
                ]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Penyesuaian stok gagal disimpan.');
        }

        return redirect()->route('stock-movements.index')
            ->with('success', 'Penyesuaian stok berhasil dicatat ke ledger.');
    }
}
