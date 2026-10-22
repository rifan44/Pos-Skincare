<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_persists_fields_used_by_the_product_controller(): void
    {
        $category = Category::create(['name' => 'Serum']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Test',
            'sku' => 'SRM-001',
            'purchase_price' => 30000,
            'margin_percent' => 20,
            'selling_price' => 36000,
            'stock' => 12,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'purchase_price' => 30000,
            'margin_percent' => 20,
            'selling_price' => 36000,
            'stock' => 12,
        ]);
    }
}
