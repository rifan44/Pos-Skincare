<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_settings_when_no_settings_record_exists(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('settings.index'));

        $response->assertOk()
            ->assertSee('value="Skincare POS"', false)
            ->assertSee('value="Solusi Cerdas Kelola"', false)
            ->assertSee('name="landing_image"', false)
            ->assertDontSee('name="about_image"', false)
            ->assertSee('id="addSocialLink"', false);

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_admin_can_save_initial_settings_when_no_record_exists(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->from(route('settings.index'))
            ->put(route('settings.update'), [
                'store_name' => 'Toko Uji',
                'store_address' => 'Alamat Uji',
                'store_phone' => '081234567890',
                'receipt_footer' => 'Terima kasih',
                'landing_title' => 'Kelola',
                'landing_text_highlight' => 'Skincare',
                'landing_description' => 'Deskripsi uji',
                'primary_color' => '#c78b7d',
                'font_family' => 'Playfair Display',
            ]);

        $response->assertRedirect(route('settings.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('settings', [
            'store_name' => 'Toko Uji',
            'store_address' => 'Alamat Uji',
            'store_phone' => '081234567890',
            'landing_title' => 'Kelola',
            'primary_color' => '#c78b7d',
        ]);
    }

    public function test_admin_can_update_an_existing_settings_record(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::create(['store_name' => 'Nama Lama']);

        $this->actingAs($admin)->put(route('settings.update'), [
            'store_name' => 'Nama Baru',
            'store_address' => 'Alamat Baru',
            'store_phone' => '081234567890',
            'receipt_footer' => 'Terima kasih',
            'landing_title' => 'Kelola',
            'landing_text_highlight' => 'Skincare',
            'landing_description' => 'Deskripsi uji',
            'primary_color' => '#c78b7d',
            'font_family' => 'Playfair Display',
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('settings', 1);
        $this->assertDatabaseHas('settings', [
            'store_name' => 'Nama Baru',
            'store_address' => 'Alamat Baru',
        ]);
    }

    public function test_admin_can_upload_a_small_landing_image_without_resolution_restriction(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('settings.update'), [
            'store_name' => 'Toko Uji',
            'store_address' => 'Alamat Uji',
            'store_phone' => '081234567890',
            'receipt_footer' => 'Terima kasih',
            'landing_title' => 'Kelola',
            'landing_text_highlight' => 'Skincare',
            'landing_description' => 'Deskripsi uji',
            'primary_color' => '#c78b7d',
            'font_family' => 'Playfair Display',
            'landing_image' => UploadedFile::fake()->image('hero.jpg', 480, 320),
        ]);

        $response->assertSessionHasNoErrors()->assertSessionHas('success');

        $setting = Setting::firstOrFail();
        $this->assertNotNull($setting->landing_image);
        Storage::disk('public')->assertExists($setting->landing_image);
    }

    public function test_admin_can_add_and_clear_custom_social_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $settings = [
            'store_name' => 'Toko Uji',
            'store_address' => 'Alamat Uji',
            'store_phone' => '081234567890',
            'receipt_footer' => 'Terima kasih',
            'landing_title' => 'Kelola',
            'landing_text_highlight' => 'Skincare',
            'landing_description' => 'Deskripsi uji',
            'primary_color' => '#c78b7d',
            'font_family' => 'Playfair Display',
            'social_links' => [
                ['name' => 'Instagram', 'url' => 'https://instagram.com/toko-uji'],
                ['name' => 'TikTok', 'url' => 'https://www.tiktok.com/@toko-uji'],
            ],
        ];

        $this->actingAs($admin)
            ->put(route('settings.update'), $settings)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame($settings['social_links'], Setting::firstOrFail()->social_links);

        unset($settings['social_links']);
        $this->actingAs($admin)
            ->put(route('settings.update'), $settings)
            ->assertSessionHasNoErrors();

        $this->assertSame([], Setting::firstOrFail()->social_links);
    }

    public function test_admin_cannot_save_non_http_social_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->from(route('settings.index'))
            ->put(route('settings.update'), [
                'store_name' => 'Toko Uji',
                'store_address' => 'Alamat Uji',
                'store_phone' => '081234567890',
                'receipt_footer' => 'Terima kasih',
                'landing_title' => 'Kelola',
                'landing_text_highlight' => 'Skincare',
                'landing_description' => 'Deskripsi uji',
                'primary_color' => '#c78b7d',
                'font_family' => 'Playfair Display',
                'social_links' => [
                    ['name' => 'Instagram', 'url' => 'javascript:alert(1)'],
                ],
            ]);

        $response->assertSessionHasErrors('social_links.0.url');
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_cashier_cannot_open_or_update_settings(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('settings.index'))
            ->assertRedirect(route('pos.index'));

        $this->actingAs($cashier)
            ->put(route('settings.update'), [])
            ->assertRedirect(route('pos.index'));

        $this->assertDatabaseCount('settings', 0);
    }
}
