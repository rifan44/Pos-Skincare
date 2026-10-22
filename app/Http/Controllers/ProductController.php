<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
        ]);

        $query = Product::with(['category', 'brand']);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();
        $categories = Category::where('category_type', 'jenis')->orderBy('name')->get();
        $brands = Category::where('category_type', 'merek')->orderBy('name')->get();

        return view('pages.products.index', compact('products', 'categories', 'brands', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('category_type', 'jenis')->orderBy('name')->get();
        $brands = Category::where('category_type', 'merek')->orderBy('name')->get();

        if ($categories->isEmpty()) {
            return redirect()->route('categories.create')->with('success', 'Silakan buat kategori jenis skincare terlebih dahulu sebelum menambah produk.');
        }

        return view('pages.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('category_type', 'jenis')
                    ->whereNull('deleted_at')),
            ],
            'brand_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('category_type', 'merek')
                    ->whereNull('deleted_at')),
            ],
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
            'brand_id' => $request->input('brand_id') ?: null,
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
        $categories = Category::where('category_type', 'jenis')->orderBy('name')->get();
        $brands = Category::where('category_type', 'merek')->orderBy('name')->get();

        return view('pages.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('category_type', 'jenis')
                    ->whereNull('deleted_at')),
            ],
            'brand_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('category_type', 'merek')
                    ->whereNull('deleted_at')),
            ],
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
            'brand_id' => $request->input('brand_id') ?: null,
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
