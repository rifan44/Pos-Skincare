<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount(['products', 'brandProducts'])
            ->orderByRaw("case category_type when 'jenis' then 0 else 1 end")
            ->orderBy('name')
            ->get();

        return view('pages.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Menampilkan halaman form tambah kategori
        return view('pages.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_type' => 'required|in:jenis,merek',
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
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
    public function edit(Category $category)
    {
        // Menampilkan form edit dengan membawa data kategori yang dipilih
        return view('pages.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_type' => 'required|in:jenis,merek',
        ]);

        if ($data['category_type'] !== $category->category_type) {
            $hasProducts = $category->products()->withTrashed()->exists()
                || $category->brandProducts()->withTrashed()->exists();

            if ($hasProducts) {
                throw ValidationException::withMessages([
                    'category_type' => 'Jenis kategori tidak dapat diubah karena sudah digunakan oleh produk. Pindahkan produk tersebut terlebih dahulu.',
                ]);
            }
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $hasProducts = $category->products()->withTrashed()->exists()
            || $category->brandProducts()->withTrashed()->exists();

        if ($hasProducts) {
            return redirect()->route('categories.index')->withErrors([
                'category' => 'Kategori ini masih digunakan oleh produk dan tidak dapat dihapus.',
            ]);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
