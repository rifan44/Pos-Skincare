<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StockMovement;
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
            'category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'sort' => 'nullable|in:az,za',
        ]);

        $query = Product::with(['category', 'brand']);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery
                ->where('name', 'like', '%'.$filters['category'].'%'));
        }

        if (! empty($filters['brand'])) {
            $query->whereHas('brand', fn ($brandQuery) => $brandQuery
                ->where('name', 'like', '%'.$filters['brand'].'%'));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('brand', fn ($brandQuery) => $brandQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $sortDirection = ($filters['sort'] ?? 'az') === 'za' ? 'desc' : 'asc';
        $products = $query->orderBy('name', $sortDirection)->get();

        return view('pages.products.index', compact('products', 'filters'));
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
            'purchase_price' => 'required|integer|min:0|max:2147483647',
            'selling_price' => 'required_without:margin_percent|integer|min:0|max:2147483647',
            'margin_percent' => [
                Rule::requiredIf(fn () => $request->input('price_source') === 'margin_percent'),
                'nullable',
                'integer',
                'min:-100',
                'max:2147483647',
            ],
            'price_source' => 'nullable|in:selling_price,margin_percent,unchanged',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi gambar
        ]);

        $purchasePrice = (int) $request->input('purchase_price');
        [$sellingPrice, $marginPercent] = $this->resolveProductPrices($request, $purchasePrice);

        // 3. Susun data yang akan disimpan
        $data = [
            'category_id' => $request->category_id,
            'brand_id' => $request->input('brand_id') ?: null,
            'name' => $request->name,
            'sku' => $request->sku,
            'purchase_price' => $request->input('purchase_price'),
            'margin_percent' => $marginPercent,
            'selling_price' => $sellingPrice,
            'stock' => $request->stock,
        ];

        // 4. Proses Upload Gambar jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        DB::transaction(function () use ($data, $request): void {
            $product = Product::create($data);

            if ((int) $product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'movement_type' => 'initial_stock',
                    'quantity_in' => $product->stock,
                    'balance_after' => $product->stock,
                    'reference' => 'INITIAL-'.$product->id,
                    'notes' => 'Stok awal saat produk dibuat.',
                ]);
            }
        });

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
            'purchase_price' => 'required|integer|min:0|max:2147483647',
            'selling_price' => 'required_without:margin_percent|integer|min:0|max:2147483647',
            'margin_percent' => [
                Rule::requiredIf(fn () => $request->input('price_source') === 'margin_percent'),
                'nullable',
                'integer',
                'min:-100',
                'max:2147483647',
            ],
            'price_source' => 'nullable|in:selling_price,margin_percent,unchanged',
            'stock' => ['prohibited'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi gambar
        ]);

        $purchasePrice = (int) $request->input('purchase_price');
        [$sellingPrice, $marginPercent] = $this->resolveProductPrices($request, $purchasePrice, $product);

        // 3. Susun data yang akan di-update
        $data = [
            'category_id' => $request->category_id,
            'brand_id' => $request->input('brand_id') ?: null,
            'name' => $request->name,
            'sku' => $request->sku,
            'purchase_price' => $request->input('purchase_price'),
            'margin_percent' => $marginPercent,
            'selling_price' => $sellingPrice,
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

    private function calculateMarginPercent(int $purchasePrice, int $sellingPrice): int
    {
        if ($purchasePrice === 0) {
            return 0;
        }

        $marginPercent = (int) round((($sellingPrice - $purchasePrice) / $purchasePrice) * 100);

        if ($marginPercent > 2147483647) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'selling_price' => 'Harga jual terlalu tinggi untuk menghitung margin produk.',
            ]);
        }

        return $marginPercent;
    }

    /**
     * Keep the price and margin in sync based on the field the user last edited.
     * Requests from older forms that only sent margin_percent remain supported.
     *
     * @return array{0: int, 1: int}
     */
    private function resolveProductPrices(Request $request, int $purchasePrice, ?Product $product = null): array
    {
        $source = $request->input('price_source');

        if ($source === 'unchanged'
            && $product
            && $request->has('purchase_price')
            && $request->has('selling_price')
            && $purchasePrice === (int) $product->purchase_price
            && (int) $request->input('selling_price') === (int) $product->selling_price) {
            return [(int) $product->selling_price, (int) $product->margin_percent];
        }

        if ($source === 'margin_percent' || (! $request->filled('selling_price') && $request->filled('margin_percent'))) {
            $marginPercent = (int) $request->input('margin_percent');
            $sellingPrice = (int) round($purchasePrice * (100 + $marginPercent) / 100);

            if ($sellingPrice < 0 || $sellingPrice > 2147483647) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'margin_percent' => 'Margin menghasilkan harga jual di luar batas nominal produk.',
                ]);
            }

            return [$sellingPrice, $marginPercent];
        }

        if ($request->filled('selling_price')) {
            $sellingPrice = (int) $request->input('selling_price');

            return [$sellingPrice, $this->calculateMarginPercent($purchasePrice, $sellingPrice)];
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'selling_price' => 'Isi harga jual atau margin keuntungan.',
        ]);
    }
}
