@extends('layouts.app')

@section('title', 'Tambah Produk | Skincare POS')

@section('content')
<div class="container py-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Tambah Produk Baru</h3>
            <p class="text-muted mb-0">Masukkan detail skincare ke dalam etalase toko.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <!-- PASTIKAN ADA ENCTYPE INI AGAR GAMBAR BISA DIKIRIM -->
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Nama Produk Skincare</label>
                    <input type="text" name="name" class="form-control bg-light" required placeholder="Contoh: Kahf Face Wash">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Jenis Skincare</label>
                    <select name="category_id" class="form-select bg-light @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Merek Skincare (Opsional)</label>
                    <select name="brand_id" class="form-select bg-light @error('brand_id') is-invalid @enderror">
                        <option value="">-- Tanpa merek --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">SKU / Barcode (Opsional)</label>
                    <input type="text" name="sku" class="form-control bg-light" placeholder="Scan barcode kemasan...">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Stok Awal</label>
                    <input type="number" name="stock" class="form-control bg-light" required min="0" placeholder="0">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Harga Beli / Modal (Rp)</label>
                    <input type="number" name="purchase_price" class="form-control bg-light" required min="0" placeholder="Contoh: 30000">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Margin Keuntungan (%)</label>
                    <input type="number" name="margin_percent" class="form-control bg-light" required min="0" placeholder="Contoh: 20">
                </div>

                <!-- INPUT GAMBAR (Memakan lebar penuh / col-12) -->
                <div class="col-12 mt-4">
                    <div class="p-3 border rounded-4 bg-light">
                        <label class="fw-bold text-dark mb-2">Foto Produk (Opsional)</label>
                        <input type="file" name="image" class="form-control bg-white" accept="image/*">
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i> Format didukung: JPG, PNG, WEBP. Maksimal 2MB. Gambar ini akan muncul di halaman Kasir.
                        </small>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-3 shadow-sm text-uppercase" style="letter-spacing: 1px;">
                <i class="fas fa-save me-2"></i> Simpan Produk Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
