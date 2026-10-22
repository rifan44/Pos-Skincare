<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_requested_summary_variables_and_stock_threshold(): void
    {
        Setting::create(['store_name' => 'Test Store']);
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum']);

        $transaction = Transaction::create([
            'total_amount' => 120000,
            'pay_amount' => 150000,
        ]);

        $lowStockProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Low Stock Product',
            'purchase_price' => 30000,
            'margin_percent' => 20,
            'selling_price' => 36000,
            'stock' => 9,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $lowStockProduct->id,
            'quantity' => 2,
            'price' => 36000,
            'hpp' => 30000,
            'subtotal' => 72000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $lowStockProduct->id,
            'quantity' => 1,
            'price' => 48000,
            'hpp' => 0,
            'subtotal' => 48000,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Stock At Threshold',
            'purchase_price' => 30000,
            'margin_percent' => 20,
            'selling_price' => 36000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.index', ['filter_date' => now()->toDateString()]));

        $response->assertOk()
            ->assertSee('Rp 120.000')
            ->assertSee('Rp 12.000')
            ->assertSeeText('1 Struk')
            ->assertSee('Low Stock Product')
            ->assertDontSee('Stock At Threshold')
            ->assertSee('HPP belum tercatat untuk 1 unit penjualan atau retur.');
    }
}
