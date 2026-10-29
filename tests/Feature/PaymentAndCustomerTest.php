<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAndCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_persists_payment_method_and_customer_snapshot(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct();

        $this->actingAs($cashier)->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 1]]),
            'pay_amount' => 15000,
            'payment_method' => 'qris',
            'customer_type' => 'reseller',
            'customer_name' => 'Reseller Uji',
            'customer_phone' => '08123456789',
        ])->assertRedirect(route('pos.index'));

        $this->assertDatabaseHas('transactions', [
            'payment_method' => 'qris',
            'customer_type' => 'reseller',
            'customer_name' => 'Reseller Uji',
            'customer_phone' => '08123456789',
        ]);
    }

    private function createProduct(): Product
    {
        $category = Category::create(['name' => 'Serum']);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Uji',
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => 2,
        ]);
    }
}
