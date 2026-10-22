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

        $request->validate([
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
            'landing_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192', 'dimensions:min_width=1200,min_height=800'],
            'about_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192', 'dimensions:min_width=1200,min_height=800'],
        ], [
            'landing_image.dimensions' => 'Gambar utama landing page harus berukuran minimal 1200 × 800 piksel.',
            'about_image.dimensions' => 'Gambar section Tentang harus berukuran minimal 1200 × 800 piksel.',
        ]);

        $data = $request->except(['logo', 'landing_image', 'about_image']);

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

        if ($request->hasFile('about_image')) {
            $aboutPath = $request->file('about_image')->store('settings', 'public');
            $data['about_image'] = $aboutPath;
        }

        $setting ??= new Setting;
        $setting->fill($data)->save();

        return back()->with('success', 'Pengaturan berhasil diperbarui secara menyeluruh!');
    }
}
