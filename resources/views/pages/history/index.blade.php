@extends('layouts.app')

@section('title', 'Riwayat Transaksi | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <!-- HEADER HALAMAN -->
    <div class="row align-items-end mb-4">
        <div class="col-sm-8 align-self-start">
            <h3 class="font-serif fw-bold text-dark mb-1">Riwayat Transaksi</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Daftar seluruh transaksi kasir yang telah berhasil diproses.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <form action="{{ route('history.index') }}" method="GET" class="d-flex flex-wrap justify-content-sm-end align-items-center gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm" aria-label="Tanggal mulai" value="{{ request('start_date') }}" style="max-width: 150px;" required>
                <input type="date" name="end_date" class="form-control form-control-sm" aria-label="Tanggal akhir" value="{{ request('end_date') }}" style="max-width: 150px;" required>
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">Filter</button>
                <a href="{{ route('history.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Reset</a>
            </form>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <!-- KARTU TABEL -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-scrollable">
                <thead class="table-light">
                    <tr>
                        <th width="15%" class="px-4 py-3 text-start">ID Transaksi</th>
                        <th width="20%" class="px-4 py-3 text-start">Tanggal & Waktu</th>
                        <th class="px-4 py-3 text-start">Uang Diterima</th>
                        <th width="20%" class="px-4 py-3 text-start">Total Nominal</th>
                        <th class="px-4 py-3 text-start">Status / Refund</th>
                        <th width="15%" class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions ?? [] as $trx)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-dark text-start">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-muted text-start">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 text-start">
                            {{ strtoupper($trx->payment_method === 'qris' ? 'QRIS' : ($trx->payment_method ?: 'cash')) }}
                            <div class="small text-muted">Rp {{ number_format($trx->pay_amount, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-3 fw-bold text-success text-start">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if($trx->return_status === 'refunded')
                                <span class="badge bg-secondary rounded-pill">Refund penuh</span>
                            @elseif($trx->return_status === 'partially_refunded')
                                <span class="badge bg-warning text-dark rounded-pill">Refund sebagian</span>
                            @else
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            @endif
                            @if($trx->refunded_amount > 0)
                                <div class="small text-muted mt-1">Rp {{ number_format($trx->refunded_amount, 0, ',', '.') }} direfund</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                            <a href="{{ route('history.print', $trx->id) }}" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3 fw-medium">
                                <i class="fas fa-print me-1"></i> Struk
                            </a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('history.returns.index', $trx) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">Retur</a>
                            @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5 fw-medium">
                            <i class="fas fa-receipt fs-2 mb-3 opacity-50 d-block"></i>
                            Belum ada riwayat transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transactions->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection
