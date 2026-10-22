<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_partially_refund_and_restock_only_resalable_units(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$transaction, $product, $detail] = $this->createSale();

        $this->actingAs($admin)
            ->get(route('history.returns.index', $transaction))
            ->assertOk()
            ->assertSee('Proses Retur')
            ->assertSee('Jumlah Retur');

        $response = $this->actingAs($admin)->post(route('history.returns.store', $transaction), [
            'reason' => 'Kemasan rusak',
            'refund_method' => 'transfer',
            'reference' => 'TRANSFER-123',
            'items' => [
                $detail->id => ['quantity' => 1, 'restocked_quantity' => 1],
            ],
        ]);

        $refund = TransactionReturn::with('details')->firstOrFail();

        $response->assertRedirect(route('history.returns.index', $transaction))
            ->assertSessionHas('success');
        $this->assertSame(10000, $refund->total_amount);
        $this->assertSame('transfer', $refund->refund_method);
        $this->assertSame('TRANSFER-123', $refund->reference);
        $this->assertSame(1, $refund->details->first()->quantity);
        $this->assertSame(1, $refund->details->first()->restocked_quantity);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame('partially_refunded', $transaction->fresh()->return_status);

        $this->actingAs($admin)
            ->get(route('history.index'))
            ->assertOk()
            ->assertSee('Refund sebagian')
            ->assertSee('Rp 10.000 direfund');

        $report = $this->actingAs($admin)->get(route('reports.index', [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $report->assertOk()
            ->assertSee('Rp 30.000')
            ->assertSee('Rp 20.000')
            ->assertSee('Rp 10.000');
    }

    public function test_returns_refund_the_discounted_amount_paid_for_each_unit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Diskon',
            'purchase_price' => 5000,
            'margin_percent' => 100,
            'selling_price' => 10000,
            'stock' => 0,
        ]);
        $transaction = Transaction::create([
            'subtotal_amount' => 18001,
            'discount_amount' => 1000,
            'total_amount' => 17001,
            'pay_amount' => 20000,
        ]);
        $detail = TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 10000,
            'discount_amount' => 1999,
            'hpp' => 5000,
            'subtotal' => 17001,
        ]);

        $firstReturn = $this->actingAs($admin)->post(route('history.returns.store', $transaction), [
            'reason' => 'Satu barang tidak sesuai',
            'refund_method' => 'cash',
            'items' => [$detail->id => ['quantity' => 1, 'restocked_quantity' => 1]],
        ]);

        $firstReturn->assertRedirect(route('history.returns.index', $transaction));
        $this->assertSame(8501, TransactionReturn::firstOrFail()->total_amount);
        $this->assertSame(1, $product->fresh()->stock);

        $this->actingAs($admin)->post(route('history.returns.store', $transaction), [
            'reason' => 'Barang kedua dikembalikan',
            'refund_method' => 'cash',
            'items' => [$detail->id => ['quantity' => 1, 'restocked_quantity' => 0]],
        ])->assertRedirect(route('history.returns.index', $transaction));

        $this->assertSame(17001, (int) TransactionReturn::where('transaction_id', $transaction->id)->sum('total_amount'));
        $this->assertSame([8501, 8500], TransactionReturn::where('transaction_id', $transaction->id)
            ->orderBy('id')
            ->pluck('total_amount')
            ->all());
        $this->assertSame('refunded', $transaction->fresh()->return_status);
    }

    public function test_return_quantity_cannot_exceed_the_unreturned_quantity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$transaction, $product, $detail] = $this->createSale();

        $this->actingAs($admin)->post(route('history.returns.store', $transaction), [
            'reason' => 'Kemasan rusak',
            'refund_method' => 'cash',
            'items' => [$detail->id => ['quantity' => 1, 'restocked_quantity' => 0]],
        ])->assertRedirect(route('history.returns.index', $transaction));

        $rejected = $this->actingAs($admin)->from(route('history.returns.index', $transaction))
            ->post(route('history.returns.store', $transaction), [
                'reason' => 'Sisa barang dikembalikan',
                'refund_method' => 'cash',
                'items' => [$detail->id => ['quantity' => 3, 'restocked_quantity' => 0]],
            ]);

        $rejected->assertRedirect(route('history.returns.index', $transaction))
            ->assertSessionHasErrors('items.'.$detail->id.'.quantity');
        $this->assertDatabaseCount('transaction_returns', 1);
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_restocked_quantity_cannot_exceed_returned_quantity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$transaction, $product, $detail] = $this->createSale();

        $response = $this->actingAs($admin)->from(route('history.returns.index', $transaction))
            ->post(route('history.returns.store', $transaction), [
                'reason' => 'Barang layak dijual',
                'refund_method' => 'cash',
                'items' => [$detail->id => ['quantity' => 1, 'restocked_quantity' => 2]],
            ]);

        $response->assertRedirect(route('history.returns.index', $transaction))
            ->assertSessionHasErrors('items.'.$detail->id.'.restocked_quantity');
        $this->assertDatabaseCount('transaction_returns', 0);
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_returns_are_limited_to_seven_days_and_admin_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$oldTransaction, , $oldDetail] = $this->createSale(now()->subDays(8));

        $expired = $this->actingAs($admin)->from(route('history.returns.index', $oldTransaction))
            ->post(route('history.returns.store', $oldTransaction), [
                'reason' => 'Retur melewati batas',
                'refund_method' => 'cash',
                'items' => [$oldDetail->id => ['quantity' => 1, 'restocked_quantity' => 0]],
            ]);
        $expired->assertRedirect(route('history.returns.index', $oldTransaction))
            ->assertSessionHasErrors('items');
        $this->assertDatabaseCount('transaction_returns', 0);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)
            ->get(route('history.returns.index', $oldTransaction))
            ->assertRedirect(route('pos.index'));
    }

    public function test_return_document_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$transaction, , $detail] = $this->createSale();

        $this->actingAs($admin)->post(route('history.returns.store', $transaction), [
            'reason' => 'Produk tidak sesuai',
            'refund_method' => 'cash',
            'items' => [$detail->id => ['quantity' => 1, 'restocked_quantity' => 0]],
        ]);

        $refund = TransactionReturn::firstOrFail();

        $this->actingAs($admin)
            ->get(route('history.returns.print', $refund))
            ->assertOk()
            ->assertSee('Bukti Retur dan Refund')
            ->assertSee('Produk Test');
    }

    private function createSale($createdAt = null): array
    {
        $category = Category::create(['name' => 'Serum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Test',
            'purchase_price' => 5000,
            'margin_percent' => 100,
            'selling_price' => 10000,
            'stock' => 2,
        ]);
        $transaction = Transaction::create([
            'total_amount' => 30000,
            'pay_amount' => 30000,
        ]);
        if ($createdAt) {
            $transaction->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->save();
        }
        $detail = TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 10000,
            'hpp' => 5000,
            'subtotal' => 30000,
        ]);

        return [$transaction, $product, $detail];
    }
}
