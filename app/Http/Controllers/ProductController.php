<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua produk beserta data kategorinya (Eager Loading)
        $products = Product::with('category')->get();
        
        return view('pages.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil data kategori untuk ditampilkan di pilihan (dropdown)
        $categories = Category::all();
        
        // Jika belum ada kategori, arahkan untuk buat kategori dulu
        if($categories->isEmpty()) {
            return redirect()->route('categories.create')->with('success', 'Silakan buat kategori terlebih dahulu sebelum menambah produk.');
        }

        return view('pages.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input (Tambahkan validasi gambar)
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'purchase_price' => 'required|integer|min:0',
            'margin_percent' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi gambar
        ]);

        // 2. Perhitungan Harga Jual (Backend)
        $keuntungan = $request->purchase_price * ($request->margin_percent / 100);
        $harga_jual = $request->purchase_price + $keuntungan;

        // 3. Susun data yang akan disimpan
        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'purchase_price' => $request->purchase_price,
            'margin_percent' => $request->margin_percent,
            'selling_price' => $harga_jual,
            'stock' => $request->stock,
        ];

        // 4. Proses Upload Gambar jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // 5. Simpan ke database
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk skincare berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Ambil semua kategori untuk pilihan dropdown
        $categories = Category::all();
        
        return view('pages.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validasi Input
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'purchase_price' => 'required|integer|min:0',
            'margin_percent' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi gambar
        ]);

        // 2. Hitung ulang harga jual
        $keuntungan = $request->purchase_price * ($request->margin_percent / 100);
        $harga_jual = $request->purchase_price + $keuntungan;

        // 3. Susun data yang akan di-update
        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'purchase_price' => $request->purchase_price,
            'margin_percent' => $request->margin_percent,
            'selling_price' => $harga_jual,
            'stock' => $request->stock,
        ];

        // 4. Proses Update Gambar jika ada yang baru diupload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // 5. Update data di database
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }
}