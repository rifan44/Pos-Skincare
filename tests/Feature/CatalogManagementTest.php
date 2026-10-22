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

        $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Serum',
            'category_type' => 'jenis',
        ])
            ->assertRedirect(route('categories.index'));

        $category = Category::query()->where('name', 'Serum')->firstOrFail();

        $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'Toner',
            'category_type' => 'jenis',
        ])
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Toner',
            'category_type' => 'jenis',
        ]);

        $this->actingAs($admin)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Jenis Skincare')
            ->assertSee('Merek Skincare')
            ->assertSee('data-category-search', false)
            ->assertSee('data-category-sort', false)
            ->assertSee('Toner');

        $this->actingAs($admin)->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_category_type_cannot_change_while_products_use_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Uji',
            'purchase_price' => 10000,
            'margin_percent' => 20,
            'selling_price' => 12000,
            'stock' => 5,
        ]);

        $this->actingAs($admin)
            ->from(route('categories.edit', $category))
            ->put(route('categories.update', $category), [
                'name' => 'Serum',
                'category_type' => 'merek',
            ])
            ->assertSessionHasErrors('category_type');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'category_type' => 'jenis',
        ]);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Serum Uji',
            'purchase_price' => 10000,
            'margin_percent' => 20,
            'selling_price' => 12000,
            'stock' => 5,
        ]);

        $this->actingAs($admin)
            ->delete(route('categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertNotSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_update_and_soft_delete_a_product_with_server_calculated_prices(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        $brand = Category::create(['name' => 'Skintific', 'category_type' => 'merek']);

        $this->actingAs($admin)
            ->get(route('products.create'))
            ->assertOk()
            ->assertSee('Jenis Skincare')
            ->assertSee('Merek Skincare');

        $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Serum Uji',
            'purchase_price' => 10000,
            'margin_percent' => 25,
            'stock' => 8,
        ])->assertRedirect(route('products.index'));

        $product = Product::query()->where('name', 'Serum Uji')->firstOrFail();
        $this->assertSame(12500, (int) $product->selling_price);
        $this->assertSame($brand->id, $product->brand_id);

        $this->actingAs($admin)->put(route('products.update', $product), [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
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

    public function test_admin_can_filter_products_by_type_brand_or_both(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $serum = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        $sunscreen = Category::create(['name' => 'Sunscreen', 'category_type' => 'jenis']);
        $kahf = Category::create(['name' => 'Kahf', 'category_type' => 'merek']);
        $skintific = Category::create(['name' => 'Skintific', 'category_type' => 'merek']);

        $this->createCatalogProduct($serum, $kahf, 'Serum Kahf');
        $this->createCatalogProduct($serum, $skintific, 'Serum Skintific');
        $this->createCatalogProduct($sunscreen, $kahf, 'Sunscreen Kahf');

        $this->actingAs($admin)
            ->get(route('products.index', ['category_id' => $serum->id]))
            ->assertOk()
            ->assertSee('Serum Kahf')
            ->assertSee('Serum Skintific')
            ->assertDontSee('Sunscreen Kahf');

        $this->actingAs($admin)
            ->get(route('products.index', ['brand_id' => $kahf->id]))
            ->assertOk()
            ->assertSee('Serum Kahf')
            ->assertSee('Sunscreen Kahf')
            ->assertDontSee('Serum Skintific');

        $this->actingAs($admin)
            ->get(route('products.index', [
                'category_id' => $serum->id,
                'brand_id' => $kahf->id,
            ]))
            ->assertOk()
            ->assertSee('Serum Kahf')
            ->assertDontSee('Serum Skintific')
            ->assertDontSee('Sunscreen Kahf');
    }

    public function test_admin_product_search_matches_brand_and_supports_reverse_sorting(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $serum = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        $brand = Category::create(['name' => 'Kahf', 'category_type' => 'merek']);
        $this->createCatalogProduct($serum, $brand, 'Alpha Serum');
        $this->createCatalogProduct($serum, $brand, 'Zeta Serum');

        $this->actingAs($admin)
            ->get(route('products.index', ['search' => 'Kahf', 'sort' => 'za']))
            ->assertOk()
            ->assertSeeInOrder(['Zeta Serum', 'Alpha Serum'])
            ->assertSee('value="za" selected', false);
    }

    public function test_product_category_fields_reject_categories_from_the_wrong_group(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $type = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        $brand = Category::create(['name' => 'Kahf', 'category_type' => 'merek']);

        $baseData = [
            'name' => 'Produk Uji',
            'purchase_price' => 10000,
            'margin_percent' => 20,
            'stock' => 5,
        ];

        $this->actingAs($admin)
            ->from(route('products.create'))
            ->post(route('products.store'), $baseData + ['category_id' => $brand->id])
            ->assertSessionHasErrors('category_id');

        $this->actingAs($admin)
            ->from(route('products.create'))
            ->post(route('products.store'), $baseData + [
                'category_id' => $type->id,
                'brand_id' => $type->id,
            ])
            ->assertSessionHasErrors('brand_id');
    }

    public function test_product_price_and_margin_inputs_stay_in_sync_on_create_and_update(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);

        $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'name' => 'Serum Margin',
            'purchase_price' => '10000',
            'selling_price' => '12000',
            'margin_percent' => '20',
            'price_source' => 'margin_percent',
            'stock' => 5,
        ])->assertRedirect(route('products.index'));

        $product = Product::query()->where('name', 'Serum Margin')->firstOrFail();
        $this->assertSame(12000, (int) $product->selling_price);
        $this->assertSame(20, (int) $product->margin_percent);
        $this->actingAs($admin)
            ->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('id="product_form"', false)
            ->assertSee('data-margin-input', false);

        $this->actingAs($admin)->put(route('products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Serum Harga Manual',
            'purchase_price' => '10000',
            'selling_price' => '15000',
            'margin_percent' => '50',
            'price_source' => 'selling_price',
            'stock' => 5,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'selling_price' => 15000,
            'margin_percent' => 50,
        ]);
    }

    public function test_cashier_can_filter_pos_catalog_by_type_and_brand(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $serum = Category::create(['name' => 'Serum', 'category_type' => 'jenis']);
        $brand = Category::create(['name' => 'Kahf', 'category_type' => 'merek']);
        $this->createCatalogProduct($serum, $brand, 'Serum Kahf');

        $this->actingAs($cashier)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee('id="category_filter"', false)
            ->assertSee('id="brand_filter"', false)
            ->assertSee('id="sort_products"', false)
            ->assertSee('id="order_discount_input"', false)
            ->assertSee('Serum Kahf')
            ->assertSee('Kahf');
    }

    private function createCatalogProduct(Category $category, Category $brand, string $name): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => $name,
            'purchase_price' => 10000,
            'margin_percent' => 20,
            'selling_price' => 12000,
            'stock' => 5,
        ]);
    }
}
