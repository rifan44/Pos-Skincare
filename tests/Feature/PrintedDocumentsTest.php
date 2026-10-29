<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintedDocumentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_render_a_sale_receipt_without_store_settings(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $category = Category::create(['name' => 'Pembersih']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pembersih Uji',
            'purchase_price' => 5000,
            'margin_percent' => 100,
            'selling_price' => 10000,
            'stock' => 4,
        ]);
        $transaction = Transaction::create([
            'total_amount' => 10000,
            'pay_amount' => 15000,
        ]);
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10000,
            'hpp' => 5000,
            'subtotal' => 10000,
        ]);

        $this->actingAs($cashier)
            ->get(route('history.print', $transaction))
            ->assertOk()
            ->assertSee('Pembersih Uji')
            ->assertSee('KEMBALI')
            ->assertSee('window.print()');
    }

    public function test_admin_can_render_the_report_print_view_without_store_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('reports.pdf', [
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition');
    }
}
