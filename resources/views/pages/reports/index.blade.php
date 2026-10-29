@extends('layouts.app')

@section('title', 'Laporan Penjualan dan Retur | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Laporan Penjualan dan Retur</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Omzet dan laba memperhitungkan diskon, HPP, serta refund pada tanggal pemrosesannya.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            @if(request('start_date') && request('end_date'))
            <div class="d-flex justify-content-sm-end gap-2">
                <a href="{{ route('reports.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" target="_blank" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="fas fa-print me-2"></i>Cetak / PDF
                </a>
                <a href="{{ route('reports.csv', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-outline-dark rounded-pill px-4 shadow-sm">
                    <i class="fas fa-file-csv me-2"></i>CSV / Excel
                </a>
            </div>
            @endif
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="fw-bold small text-muted mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control bg-light" value="{{ old('start_date', request('start_date')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="fw-bold small text-muted mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control bg-light" value="{{ old('end_date', request('end_date')) }}" required>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill fw-medium">Tampilkan</button>
                <a href="{{ route('reports.index') }}" class="btn btn-light border w-100 rounded-pill fw-medium text-dark">Reset</a>
            </div>
        </form>
    </div>

    @if(request('start_date'))
    @if($hppBelumDiketahui > 0)
    <div class="alert alert-warning border-0 shadow-sm rounded-4" role="alert">
        HPP belum tercatat untuk {{ $hppBelumDiketahui }} unit penjualan atau retur. Laba kotor hanya mencakup barang dengan HPP tersimpan.
    </div>
    @endif
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background-color: var(--card-bg);">
                <span class="fw-bold text-muted small text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Omzet Kotor</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">Rp {{ number_format($grossOmzet, 0, ',', '.') }}</h2>
                <small class="text-muted">{{ $totalItem }} unit terjual</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background-color: var(--bg-cream);">
                <span class="fw-bold text-muted small text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Diskon</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</h2>
                <small class="text-muted">Diskon produk dan transaksi</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background-color: var(--bg-cream);">
                <span class="fw-bold text-muted small text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Refund</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">Rp {{ number_format($totalRefund, 0, ',', '.') }}</h2>
                <small class="text-muted">Refund yang diproses pada periode ini</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background-color: var(--card-bg);">
                <span class="fw-bold text-muted small text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Omzet Bersih</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background-color: var(--bg-cream);">
                <span class="fw-bold text-muted small text-uppercase mb-2 d-block" style="letter-spacing: 1px;">HPP Bersih Tercatat</span>
                <h2 class="fw-extrabold text-dark mb-0 font-serif">Rp {{ number_format($totalModal, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden" style="background-color: var(--primary-color);">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-chart-pie fa-4x text-white"></i>
                </div>
                <span class="fw-bold text-white small text-uppercase mb-2 d-block opacity-75" style="letter-spacing: 1px;">Laba Kotor Tercatat</span>
                <h2 class="fw-extrabold text-white mb-0 font-serif">Rp {{ number_format($totalLaba, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-scrollable">
                <thead class="table-light">
                    <tr>
                        <th width="15%" class="px-4 py-3 text-start">Tanggal</th>
                        <th class="px-4 py-3 text-center">Transaksi</th>
                        <th class="px-4 py-3 text-center">Terjual / Retur</th>
                        <th class="px-4 py-3 text-end">Diskon</th>
                        <th class="px-4 py-3 text-end">Refund</th>
                        <th class="px-4 py-3 text-end">HPP Bersih</th>
                        <th class="px-4 py-3 text-end">Omzet Bersih</th>
                        <th class="px-4 py-3 text-end">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyReports as $report)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-dark text-start">{{ \Carbon\Carbon::parse($report['date'])->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center text-muted">{{ $report['total_transactions'] }} struk / {{ $report['total_refunds'] }} refund</td>
                        <td class="px-4 py-3 text-center text-muted">{{ $report['total_items'] }} / {{ $report['returned_items'] }} unit</td>
                        <td class="px-4 py-3 text-end text-muted">Rp {{ number_format($report['discounts'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-end text-muted">Rp {{ number_format($report['refunds'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-end text-muted">Rp {{ number_format($report['modal'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 fw-bold text-dark text-end">Rp {{ number_format($report['omzet'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 fw-bold text-success text-end">Rp {{ number_format($report['laba'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5 fw-medium">Belum ada transaksi atau retur pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
