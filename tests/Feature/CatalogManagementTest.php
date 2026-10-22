<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_soft_delete_a_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('categories.index'))->assertOk();

        $this->actingAs($admin)->post(route('categories.store'), ['name' => 'Serum'])
            ->assertRedirect(route('categories.index'));

        $category = Category::query()->where('name', 'Serum')->firstOrFail();

        $this->actingAs($admin)->put(route('categories.update', $category), ['name' => 'Toner'])
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Toner']);

        $this->actingAs($admin)->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_update_and_soft_delete_a_product_with_server_calculated_prices(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum']);

        $this->actingAs($admin)->get(route('products.create'))->assertOk();

        $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'name' => 'Serum Uji',
            'purchase_price' => 10000,
            'margin_percent' => 25,
            'stock' => 8,
        ])->assertRedirect(route('products.index'));

        $product = Product::query()->where('name', 'Serum Uji')->firstOrFail();
        $this->assertSame(12500, (int) $product->selling_price);

        $this->actingAs($admin)->put(route('products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Serum Uji Revisi',
            'purchase_price' => 20000,
            'margin_percent' => 30,
            'stock' => 5,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Serum Uji Revisi',
            'selling_price' => 26000,
            'stock' => 5,
        ]);

        $this->actingAs($admin)->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
