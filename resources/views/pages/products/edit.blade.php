@extends('layouts.app')

@section('title', 'Edit Produk | Skincare POS')

@section('content')
<div class="container py-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Edit Data Produk</h3>
            <p class="text-muted mb-0">Perbarui harga, stok, atau gambar produk.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <!-- PASTIKAN ADA ENCTYPE INI -->
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Nama Produk Skincare</label>
                    <input type="text" name="name" class="form-control bg-light" value="{{ $product->name }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Jenis Skincare</label>
                    <select name="category_id" class="form-select bg-light @error('category_id') is-invalid @enderror" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Merek Skincare (Opsional)</label>
                    <select name="brand_id" class="form-select bg-light @error('brand_id') is-invalid @enderror">
                        <option value="">-- Tanpa merek --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">SKU / Barcode (Opsional)</label>
                    <input type="text" name="sku" class="form-control bg-light" value="{{ $product->sku }}">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Stok Tersedia</label>
                    <input type="number" name="stock" class="form-control bg-light" value="{{ $product->stock }}" required min="0">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Harga Beli / Modal (Rp)</label>
                    <input type="number" name="purchase_price" class="form-control bg-light" value="{{ $product->purchase_price }}" required min="0">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold small text-muted mb-1">Margin Keuntungan (%)</label>
                    <input type="number" name="margin_percent" class="form-control bg-light" value="{{ $product->margin_percent }}" required min="0">
                </div>
                
                <!-- INPUT GAMBAR DAN PREVIEW -->
                <div class="col-12 mt-4">
                    <div class="p-3 border rounded-4 bg-light d-flex flex-column flex-md-row align-items-center gap-4">
                        
                        <!-- Preview Gambar Lama -->
                        <div class="text-center">
                            <label class="fw-bold small text-muted mb-2 d-block">Gambar Saat Ini</label>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded-3 border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded-3 border d-flex align-items-center justify-content-center bg-white text-muted" style="width: 100px; height: 100px;">
                                    <i class="fas fa-image fs-3"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Form Ganti Gambar -->
                        <div class="flex-grow-1 w-100 mt-3 mt-md-0">
                            <label class="fw-bold text-dark mb-2">Ganti Foto Produk</label>
                            <input type="file" name="image" class="form-control bg-white" accept="image/*">
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengubah gambar lama.
                            </small>
                        </div>

                    </div>
                </div>
            </div>

            <hr class="my-4">
            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-3 shadow-sm text-uppercase" style="letter-spacing: 1px;">
                <i class="fas fa-check-circle me-2"></i> Simpan Perubahan Produk
            </button>
        </form>
    </div>
</div>
@endsection
