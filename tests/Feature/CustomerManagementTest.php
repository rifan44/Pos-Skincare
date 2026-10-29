<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_customer_and_customer_appears_in_pos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'Reseller Uji',
            'customer_type' => 'reseller',
            'phone' => '08123456789',
            'is_active' => 1,
        ])->assertRedirect(route('customers.index'));

        $this->actingAs($admin)->get(route('pos.index'))
            ->assertOk()
            ->assertSee('Reseller Uji');
    }

    public function test_checkout_links_selected_customer_and_uses_its_snapshot(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $customer = Customer::create([
            'name' => 'Customer Tersimpan',
            'customer_type' => 'reseller',
            'phone' => '0800000000',
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Serum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Uji',
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => 2,
        ]);

        $this->actingAs($cashier)->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 1]]),
            'pay_amount' => 15000,
            'customer_id' => $customer->id,
            'customer_type' => 'regular',
            'payment_method' => 'cash',
        ])->assertRedirect(route('pos.index'));

        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'customer_type' => 'reseller',
            'customer_name' => 'Customer Tersimpan',
            'customer_phone' => '0800000000',
        ]);
    }

    public function test_customer_with_transactions_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Terpakai', 'is_active' => true]);
        $customer->transactions()->create([
            'total_amount' => 1000,
            'pay_amount' => 1000,
        ]);

        $this->actingAs($admin)->delete(route('customers.destroy', $customer))
            ->assertSessionHasErrors('customer');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'deleted_at' => null]);
    }
}
