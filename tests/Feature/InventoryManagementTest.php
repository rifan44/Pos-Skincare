<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_received_purchase_increases_stock_and_creates_ledger(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'Supplier Test']);
        $product = $this->createProduct(3);

        $response = $this->actingAs($admin)->post(route('purchases.store'), [
            'source' => 'supplier',
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 4,
                'unit_cost' => 9000,
            ]],
        ]);

        $purchase = Purchase::firstOrFail();
        $response->assertRedirect(route('purchases.index'));
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'purchase_in',
            'quantity_in' => 4,
            'balance_after' => 7,
            'source_id' => $purchase->id,
        ]);
    }

    public function test_purchase_cancellation_is_idempotent_and_reverses_stock_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct(3);
        $purchase = Purchase::create([
            'created_by' => $admin->id,
            'source' => 'self_purchase',
            'status' => 'received',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 20000,
            'received_at' => now(),
        ]);
        $purchase->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_cost' => 10000,
            'subtotal' => 20000,
        ]);
        $product->increment('stock', 2);

        $this->actingAs($admin)->post(route('purchases.cancel', $purchase))->assertRedirect();
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('purchases', ['id' => $purchase->id, 'status' => 'cancelled']);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_sale_stores_customer_type_and_creates_outbound_ledger(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = $this->createProduct(5);

        $this->actingAs($cashier)->post(route('pos.store'), [
            'cart_data' => json_encode([['id' => $product->id, 'qty' => 2]]),
            'pay_amount' => 30000,
            'customer_type' => 'reseller',
        ])->assertRedirect(route('pos.index'));

        $this->assertDatabaseHas('transactions', ['customer_type' => 'reseller']);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'sale_out',
            'quantity_out' => 2,
            'balance_after' => 3,
        ]);
    }

    public function test_draft_purchase_does_not_change_stock_until_received(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct(3);

        $this->actingAs($admin)->post(route('purchases.store'), [
            'source' => 'self_purchase',
            'status' => 'draft',
            'purchase_date' => now()->toDateString(),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 4,
                'unit_cost' => 9000,
            ]],
        ])->assertRedirect(route('purchases.index'));

        $purchase = Purchase::firstOrFail();
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertNull($purchase->received_at);

        $this->actingAs($admin)->post(route('purchases.receive', $purchase))->assertRedirect();
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    private function createProduct(int $stock): Product
    {
        $category = Category::create(['name' => 'Serum']);

        return Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-'.$category->id.'-'.$stock,
            'name' => 'Serum Test '.$stock,
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => $stock,
        ]);
    }
}
