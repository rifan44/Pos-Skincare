@extends('layouts.app')

@section('title', 'Kelola Kategori | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <!-- HEADER HALAMAN (STANDAR BAKU) -->
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Kategori Produk</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola pengelompokan jenis barang skincare di toko Anda.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('categories.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Tambah Kategori
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KARTU TABEL -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="8%" class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3 text-start">Nama Kategori</th>
                        <th width="20%" class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                    <tr>
                        <td class="px-4 py-3 text-center text-muted">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 fw-bold text-dark">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 me-2 fw-medium">
                                Edit
                            </a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-5 fw-medium">
                            <i class="fas fa-folder-open fs-2 mb-3 opacity-50 d-block"></i>
                            Belum ada kategori terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection