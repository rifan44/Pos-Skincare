<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_navigation_uses_real_sections_and_store_contact_settings(): void
    {
        Setting::create([
            'store_name' => 'Test Skincare',
            'store_address' => 'Jl. Mawar 10',
            'store_phone' => '+62 812-3456-7890',
        ]);

        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee(route('landing').'#features', false)
            ->assertSee(route('landing').'#about', false)
            ->assertSee(route('landing').'#contact', false)
            ->assertSee('tel:+6281234567890', false)
            ->assertSee('data-nav-link', false)
            ->assertSee('hidden.bs.offcanvas', false)
            ->assertSee('Jl. Mawar 10')
            ->assertDontSee('href="#"', false)
            ->assertDontSee('href="'.route('landing').'#features" data-bs-dismiss="offcanvas"', false)
            ->assertDontSee('halo@skincarepos.com')
            ->assertDontSee('Kirim Pesan');
    }

    public function test_cashier_landing_action_leads_to_the_pos_page(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($cashier)->get(route('landing'));

        $response->assertOk()
            ->assertSee(route('pos.index'), false)
            ->assertDontSee(route('dashboard.index'), false);
    }

    public function test_landing_page_renders_the_configured_about_image(): void
    {
        Setting::create([
            'store_name' => 'Test Skincare',
            'about_image' => 'settings/about.jpg',
        ]);

        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee(asset('storage/settings/about.jpg'), false)
            ->assertSee('alt="Tentang Test Skincare"', false);
    }

    public function test_history_title_stays_at_the_top_of_its_page_header(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('history.index'));

        $response->assertOk()
            ->assertSee('col-sm-8 align-self-start', false)
            ->assertSee('col-sm-4 text-sm-end mt-3 mt-sm-0', false);
    }
}
