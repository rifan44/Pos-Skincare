<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_catalog_and_inventory_hubs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Katalog Produk')
            ->assertSee(route('products.index'), false)
            ->assertSee(route('categories.index'), false);

        $this->actingAs($admin)
            ->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('Inventori')
            ->assertSee(route('suppliers.index'), false)
            ->assertSee(route('purchases.index'), false)
            ->assertSee(route('stock-movements.index'), false);
    }

    public function test_cashier_cannot_open_management_hubs(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('catalog.index'))
            ->assertRedirect(route('pos.index'));

        $this->actingAs($cashier)
            ->get(route('inventory.index'))
            ->assertRedirect(route('pos.index'));
    }
}
