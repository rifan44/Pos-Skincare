<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data kategori dari tabel database
        $categories = \App\Models\Category::all();
        
        // Memanggil halaman view dan mengirimkan variabel $categories
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
     // 1. Validasi: pastikan nama kategori wajib diisi
     $request->validate([
         'name' => 'required|string|max:255'
     ]);

     // 2. Simpan data ke database
     Category::create([
         'name' => $request->name
     ]);

     // 3. Kembali ke halaman daftar kategori dengan pesan sukses
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
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Menyimpan perubahan ke database
        $category->update([
            'name' => $request->name
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Menghapus data dari database
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}