<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_recalculates_prices_and_total_from_database_and_reduces_stock(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(stock: 5);

        $response = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([[
                'id' => $product->id,
                'qty' => 2,
                'price' => 1,
                'subtotal' => 2,
                'name' => 'Forged name',
            ]]),
            'total_amount' => 2,
            'pay_amount' => 30000,
        ]);

        $transaction = Transaction::first();

        $response->assertRedirect(route('pos.index'))
            ->assertSessionHas('print_id', $transaction->id);
        $this->assertSame(30000, $transaction->total_amount);
        $this->assertSame(30000, $transaction->pay_amount);
        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 15000,
            'hpp' => 10000,
            'subtotal' => 30000,
        ]);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_checkout_applies_product_and_order_discounts_to_receipt_and_report(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(stock: 5);

        $response = $this->actingAs($cashier)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([[
                'id' => $product->id,
                'qty' => 2,
                'discount' => 5000,
            ]]),
            'discount_amount' => 2500,
            'pay_amount' => 22500,
        ]);

        $transaction = Transaction::firstOrFail();
        $detail = $transaction->details()->firstOrFail();

        $response->assertRedirect(route('pos.index'))
            ->assertSessionHas('print_id', $transaction->id);
        $this->assertSame(25000, $transaction->subtotal_amount);
        $this->assertSame(2500, $transaction->discount_amount);
        $this->assertSame(22500, $transaction->total_amount);
        $this->assertSame(5000, $detail->discount_amount);
        $this->assertSame(22500, $detail->subtotal);
        $this->assertSame(3, $product->fresh()->stock);

        $this->actingAs($cashier)
            ->get(route('history.print', $transaction))
            ->assertOk()
            ->assertSee('Diskon produk')
            ->assertSee('Diskon transaksi')
            ->assertSee('22.500');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('reports.index', [
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Rp 30.000')
            ->assertSee('Rp 7.500')
            ->assertSee('Rp 22.500')
            ->assertSee('Rp 2.500');
    }

    public function test_discounts_cannot_exceed_the_product_or_cart_value(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(stock: 5);

        $excessiveProductDiscount = $this->actingAs($cashier)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 1, 'discount' => 15001]]),
            'pay_amount' => 0,
        ]);
        $excessiveProductDiscount->assertRedirect('/pos')->assertSessionHasErrors('cart_data');

        $excessiveOrderDiscount = $this->actingAs($cashier)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 1, 'discount' => 1000]]),
            'discount_amount' => 14001,
            'pay_amount' => 0,
        ]);
        $excessiveOrderDiscount->assertRedirect('/pos')->assertSessionHasErrors('discount_amount');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_order_discount_is_allocated_across_lines_without_losing_rupiah(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $firstProduct = $this->createProduct(stock: 5);
        $secondProduct = $this->createProduct(stock: 5);

        $this->actingAs($cashier)->post(route('pos.store'), [
            'cart_data' => json_encode([
                ['id' => $firstProduct->id, 'qty' => 1, 'discount' => 0],
                ['id' => $secondProduct->id, 'qty' => 1, 'discount' => 0],
            ]),
            'discount_amount' => 1,
            'pay_amount' => 29999,
        ])->assertRedirect(route('pos.index'));

        $transaction = Transaction::firstOrFail();
        $lineTotals = $transaction->details()->orderBy('id')->pluck('subtotal')->all();

        $this->assertSame(30000, $transaction->subtotal_amount);
        $this->assertSame(29999, $transaction->total_amount);
        $this->assertSame([14999, 15000], $lineTotals);
        $this->assertSame($transaction->total_amount, (int) array_sum($lineTotals));
    }

    public function test_insufficient_stock_creates_no_transaction_and_does_not_change_stock(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(stock: 1);

        $response = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 2]]),
            'total_amount' => 30000,
            'pay_amount' => 30000,
        ]);

        $response->assertRedirect('/pos')->assertSessionHasErrors('cart_data');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('transaction_details', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_payment_below_recalculated_total_is_rejected_without_changing_stock(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(stock: 5);

        $response = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 2]]),
            'total_amount' => 1,
            'pay_amount' => 1,
        ]);

        $response->assertRedirect('/pos')->assertSessionHasErrors('pay_amount');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_report_can_be_downloaded_as_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('reports.csv', [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertHeader('content-disposition');
    }

    public function test_invalid_quantities_and_duplicate_products_are_rejected(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(stock: 5);

        $invalidQuantity = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 0]]),
            'pay_amount' => 1,
        ]);
        $invalidQuantity->assertRedirect('/pos')->assertSessionHasErrors('items.0.qty');

        $duplicateProduct = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => json_encode([
                ['id' => $product->id, 'qty' => 1],
                ['id' => $product->id, 'qty' => 1],
            ]),
            'pay_amount' => 30000,
        ]);
        $duplicateProduct->assertRedirect('/pos')->assertSessionHasErrors('items.1.id');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_malformed_or_empty_cart_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $malformed = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => '{not-json',
            'pay_amount' => 1,
        ]);
        $malformed->assertRedirect('/pos')->assertSessionHasErrors('cart_data');

        $empty = $this->actingAs($user)->from('/pos')->post(route('pos.store'), [
            'cart_data' => '[]',
            'pay_amount' => 1,
        ]);
        $empty->assertRedirect('/pos')->assertSessionHasErrors('items');

        $this->assertDatabaseCount('transactions', 0);
    }

    private function createProduct(int $stock): Product
    {
        $category = Category::create(['name' => 'Serum']);

        return Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-'.$category->id,
            'name' => 'Serum Test',
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => $stock,
        ]);
    }
}
