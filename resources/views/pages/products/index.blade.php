@extends('layouts.app')

@section('title', 'Data Produk | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Data Produk Skincare</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola produk, jenis skincare, merek, harga jual, dan stok tersedia.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('products.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('products.index') }}" method="GET" class="card border-0 shadow-sm rounded-4 p-3 p-lg-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label for="search" class="form-label small fw-bold text-muted">Cari Produk</label>
                <input type="search" id="search" name="search" class="form-control bg-light" value="{{ request('search') }}" placeholder="Nama produk atau SKU">
            </div>
            <div class="col-md-5 col-lg-3">
                <label for="category_id" class="form-label small fw-bold text-muted">Jenis Skincare</label>
                <select id="category_id" name="category_id" class="form-select bg-light">
                    <option value="">Semua jenis</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 col-lg-3">
                <label for="brand_id" class="form-label small fw-bold text-muted">Merek</label>
                <select id="brand_id" name="brand_id" class="form-select bg-light">
                    <option value="">Semua merek</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark rounded-pill flex-grow-1">Filter</button>
                <a href="{{ route('products.index') }}" class="btn btn-light border rounded-pill">Reset</a>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="px-4 py-3 text-center">No</th>
                        <th width="8%" class="px-4 py-3 text-center">Foto</th>
                        <th width="14%" class="px-4 py-3 text-start">Jenis</th>
                        <th width="14%" class="px-4 py-3 text-start">Merek</th>
                        <th class="px-4 py-3 text-start">Nama Produk</th>
                        <th width="12%" class="px-4 py-3 text-start">HPP</th>
                        <th width="12%" class="px-4 py-3 text-start">Harga Jual</th>
                        <th width="8%" class="px-4 py-3 text-center">Stok</th>
                        <th width="15%" class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                    <tr>
                        <td class="px-4 py-3 text-center text-muted">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="rounded-3 border" style="width: 45px; height: 45px; object-fit: cover;">
                            @else
                                <div class="rounded-3 border d-inline-flex align-items-center justify-content-center bg-light text-muted" style="width: 45px; height: 45px;">
                                    <i class="fas fa-image" style="font-size: 1rem;"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-start"><span class="badge bg-light text-dark border px-2 py-1">{{ $product->category->name ?? '-' }}</span></td>
                        <td class="px-4 py-3 text-start"><span class="badge text-white px-2 py-1" style="background-color: var(--primary-color);">{{ $product->brand->name ?? '-' }}</span></td>
                        <td class="px-4 py-3 fw-bold text-dark">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-muted text-start">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 fw-bold text-success text-start">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center"><span class="badge {{ $product->stock < 10 ? 'bg-danger' : 'bg-success' }} rounded-pill px-2">{{ $product->stock }}</span></td>
                        <td class="px-4 py-3">
                            <!-- PERBAIKAN TOMBOL AKSI -->
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">
                                    Edit
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5 fw-medium">
                            {{ request()->hasAny(['search', 'category_id', 'brand_id']) ? 'Tidak ada produk yang cocok dengan filter.' : 'Belum ada produk terdaftar.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
