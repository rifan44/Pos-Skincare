<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFinancialSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_uses_hpp_snapshot_and_flags_legacy_details_without_hpp(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Test',
            'purchase_price' => 50000,
            'margin_percent' => 20,
            'selling_price' => 60000,
            'stock' => 10,
        ]);
        $transaction = Transaction::create([
            'total_amount' => 150000,
            'pay_amount' => 150000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 60000,
            'hpp' => 40000,
            'subtotal' => 120000,
        ]);
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 30000,
            'hpp' => 0,
            'subtotal' => 30000,
        ]);

        $product->update(['purchase_price' => 90000]);

        $response = $this->actingAs($admin)->get(route('reports.index', [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertOk()
            ->assertSee('Rp 150.000')
            ->assertSee('Rp 40.000')
            ->assertSee('HPP belum tercatat untuk 1 unit');
    }

    public function test_report_requires_a_complete_ordered_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('reports.index', ['start_date' => now()->toDateString()]))
            ->assertRedirect()
            ->assertSessionHasErrors('end_date');

        $this->actingAs($admin)
            ->get(route('reports.index', [
                'start_date' => now()->toDateString(),
                'end_date' => now()->subDay()->toDateString(),
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('end_date');
    }
}
