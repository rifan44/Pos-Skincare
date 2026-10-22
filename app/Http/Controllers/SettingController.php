<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?? new Setting([
            'store_name' => 'Skincare POS',
            'store_address' => '',
            'store_phone' => '',
            'receipt_footer' => '',
            'landing_title' => 'Solusi Cerdas Kelola',
            'landing_text_highlight' => 'Toko Skincare',
            'landing_description' => '',
            'primary_color' => '#ffb6b9',
            'font_family' => 'Plus Jakarta Sans',
        ]);

        return view('pages.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'receipt_footer' => 'nullable|string',
            'landing_title' => 'required|string|max:255',
            'landing_text_highlight' => 'required|string|max:255',
            'landing_description' => 'required|string',
            'primary_color' => 'required|string|max:7',
            'font_family' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'landing_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:8192',
            'social_links' => 'nullable|array|max:10',
            'social_links.*' => 'array:name,url',
            'social_links.*.name' => 'nullable|string|max:40',
            'social_links.*.url' => 'nullable|url:http,https|max:255',
        ], [
            'landing_image.image' => 'File gambar utama harus berupa gambar yang valid.',
            'landing_image.mimes' => 'Format gambar utama harus JPG, PNG, WebP, GIF, atau BMP.',
            'social_links.max' => 'Maksimal 10 tautan media sosial yang dapat ditambahkan.',
            'social_links.*.url.url' => 'Tautan media sosial harus menggunakan URL HTTP atau HTTPS yang valid.',
        ]);

        $data = $request->except(['logo', 'landing_image', 'about_image', 'social_links']);
        $data['social_links'] = collect($validated['social_links'] ?? [])
            ->map(fn (array $link) => [
                'name' => trim($link['name'] ?? ''),
                'url' => trim($link['url'] ?? ''),
            ])
            ->filter(fn (array $link) => $link['name'] !== '' && $link['url'] !== '')
            ->values()
            ->all();

        // Handle Upload Logo Bulat Navbar
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('settings', 'public');
            $data['logo'] = $logoPath;
        }

        // Handle Upload Gambar Landing Page
        if ($request->hasFile('landing_image')) {
            $landingPath = $request->file('landing_image')->store('settings', 'public');
            $data['landing_image'] = $landingPath;
        }

        $setting ??= new Setting;
        $setting->fill($data)->save();

        return back()->with('success', 'Pengaturan berhasil diperbarui secara menyeluruh!');
    }
}
