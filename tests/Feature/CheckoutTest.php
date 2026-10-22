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
            'sku' => 'SKU-' . $category->id,
            'name' => 'Serum Test',
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => $stock,
        ]);
    }
}
