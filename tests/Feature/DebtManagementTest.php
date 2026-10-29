<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_debt_and_record_partial_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('debts.store'), [
            'direction' => 'payable',
            'party_type' => 'supplier',
            'party_name' => 'Supplier Uji',
            'reference' => 'INV-001',
            'amount' => 100000,
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect();

        $debt = Debt::firstOrFail();

        $this->actingAs($admin)->post(route('debts.payments.store', $debt), [
            'amount' => 40000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'transfer',
            'reference' => 'TRF-001',
        ])->assertRedirect(route('debts.show', $debt));

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'paid_amount' => 40000,
            'status' => 'partial',
        ]);
        $this->assertDatabaseHas('debt_payments', [
            'debt_id' => $debt->id,
            'amount' => 40000,
            'payment_method' => 'transfer',
        ]);
    }

    public function test_payment_cannot_exceed_remaining_balance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $debt = Debt::create([
            'direction' => 'receivable',
            'party_type' => 'customer',
            'party_name' => 'Customer Uji',
            'amount' => 50000,
            'paid_amount' => 20000,
            'status' => 'partial',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->from(route('debts.show', $debt))
            ->post(route('debts.payments.store', $debt), [
                'amount' => 30001,
                'payment_date' => now()->toDateString(),
                'payment_method' => 'cash',
            ])
            ->assertRedirect(route('debts.show', $debt))
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('debt_payments', 0);
    }

    public function test_debt_with_payment_cannot_be_cancelled(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $debt = Debt::create([
            'direction' => 'payable',
            'party_type' => 'supplier',
            'party_name' => 'Supplier Uji',
            'amount' => 50000,
            'paid_amount' => 10000,
            'status' => 'partial',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->post(route('debts.cancel', $debt))
            ->assertSessionHasErrors('debt');
        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'partial']);
    }

    public function test_credit_checkout_creates_receivable_for_unpaid_balance(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $category = Category::create(['name' => 'Serum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Kredit',
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => 2,
        ]);

        $this->actingAs($cashier)->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 1]]),
            'pay_amount' => 5000,
            'payment_method' => 'credit',
            'customer_name' => 'Customer Kredit',
            'customer_phone' => '08123456789',
        ])->assertRedirect(route('pos.index'));

        $this->assertDatabaseHas('debts', [
            'direction' => 'receivable',
            'party_name' => 'Customer Kredit',
            'amount' => 10000,
            'reference' => 'TRX-1',
            'status' => 'open',
        ]);
    }
}
