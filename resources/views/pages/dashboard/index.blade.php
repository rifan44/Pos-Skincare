@extends('layouts.app')

@section('title', 'Dashboard | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <!-- HEADER HALAMAN -->
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Dashboard Analitik</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Ringkasan performa penjualan dan pantauan stok toko hari ini.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <form action="{{ route('dashboard.index') }}" method="GET" class="d-flex justify-content-sm-end align-items-center gap-2">
                <input type="date" name="filter_date" class="form-control form-control-sm bg-white" value="{{ $filterDate }}" style="max-width: 150px;">
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-medium">Filter</button>
            </form>
        </div>
    </div>

    <!-- KARTU RINGKASAN -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <i class="fas fa-wallet fa-4x text-dark"></i>
                </div>
                <span class="fw-bold text-muted small text-uppercase mb-1 d-block" style="letter-spacing: 1px;">Omzet Bersih</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}</h2>
                <small class="text-muted">Refund pada tanggal terpilih: Rp {{ number_format($totalRefund, 0, ',', '.') }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden" style="background-color: var(--bg-cream);">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <i class="fas fa-chart-line fa-4x" style="color: var(--primary-color);"></i>
                </div>
                <span class="fw-bold text-muted small text-uppercase mb-1 d-block" style="letter-spacing: 1px;">Laba Kotor Tercatat</span>
                <h2 class="fw-extrabold mb-0 font-serif" style="color: var(--primary-color);">Rp {{ number_format($totalLaba, 0, ',', '.') }}</h2>
                @if($hppBelumDiketahui > 0)
                    <small class="text-muted">HPP belum tercatat untuk {{ $hppBelumDiketahui }} unit penjualan atau retur.</small>
                @else
                    <small class="text-muted">Dihitung dari snapshot harga dan HPP transaksi.</small>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <i class="fas fa-receipt fa-4x text-dark"></i>
                </div>
                <span class="fw-bold text-muted small text-uppercase mb-1 d-block" style="letter-spacing: 1px;">Total Transaksi</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">{{ $totalTransaksi ?? 0 }} <span class="fs-6 text-muted font-sans fw-medium">Struk</span></h2>
            </div>
        </div>
    </div>

    <!-- TABEL PERINGATAN STOK -->
    <div class="d-flex align-items-center mb-3 mt-2">
        <i class="fas fa-exclamation-triangle text-warning fs-5 me-2"></i>
        <h5 class="fw-bold text-dark mb-0 font-serif">Peringatan Stok Menipis</h5>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3 text-start">Nama Produk</th>
                        <th width="20%" class="px-4 py-3 text-start">Kategori</th>
                        <th width="15%" class="px-4 py-3 text-center">Sisa Stok</th>
                        <th width="15%" class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokMenipis ?? [] as $index => $produk)
                    <tr>
                        <td class="px-4 py-3 text-center text-muted">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 fw-bold text-dark">{{ $produk->name }}</td>
                        <td class="px-4 py-3 text-muted text-start">{{ $produk->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">{{ $produk->stock }} Tersisa</span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('products.edit', $produk->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">Restock</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5 fw-medium">
                            <i class="fas fa-check-circle fs-2 mb-3 d-block" style="color: var(--primary-color); opacity: 0.5;"></i>
                            Kondisi stok aman. Tidak ada produk yang menipis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
