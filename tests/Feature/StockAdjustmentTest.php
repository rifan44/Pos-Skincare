<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_adjust_stock_and_a_movement_is_recorded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct(5);

        $this->actingAs($admin)->post(route('stock-adjustments.store'), [
            'product_id' => $product->id,
            'direction' => 'out',
            'quantity' => 2,
            'reason' => 'Dua unit rusak saat stok opname.',
        ])->assertRedirect(route('stock-movements.index'));

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'adjustment_out',
            'quantity_out' => 2,
            'balance_after' => 3,
            'user_id' => $admin->id,
        ]);
    }

    public function test_adjustment_cannot_reduce_stock_below_zero(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct(1);

        $this->actingAs($admin)
            ->from(route('stock-adjustments.create'))
            ->post(route('stock-adjustments.store'), [
                'product_id' => $product->id,
                'direction' => 'out',
                'quantity' => 2,
                'reason' => 'Koreksi stok.',
            ])
            ->assertRedirect(route('stock-adjustments.create'))
            ->assertSessionHasErrors('quantity');

        $this->assertSame(1, $product->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    private function createProduct(int $stock): Product
    {
        $category = Category::create(['name' => 'Serum']);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Uji',
            'sku' => 'SKU-'.$stock,
            'purchase_price' => 10000,
            'margin_percent' => 50,
            'selling_price' => 15000,
            'stock' => $stock,
        ]);
    }
}
