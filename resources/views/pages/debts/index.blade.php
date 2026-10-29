@extends('layouts.app')
@section('title', 'Utang Piutang | Skincare POS')
@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="font-serif fw-bold mb-1">Utang & Piutang</h3><p class="text-muted mb-0">Pantau kewajiban dan tagihan toko tanpa memenuhi navbar utama.</p></div><a href="{{ route('debts.create') }}" class="btn btn-dark rounded-pill"><i class="fas fa-plus me-2"></i>Catat Baru</a></div>
    @include('partials.flash')
    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="card border-0 shadow-sm rounded-4 p-3"><div class="small text-muted">Utang belum lunas</div><div class="fs-4 fw-bold text-danger">Rp {{ number_format($summary['payable'], 0, ',', '.') }}</div></div></div>
        <div class="col-md-6"><div class="card border-0 shadow-sm rounded-4 p-3"><div class="small text-muted">Piutang belum tertagih</div><div class="fs-4 fw-bold text-success">Rp {{ number_format($summary['receivable'], 0, ',', '.') }}</div></div></div>
    </div>
    <form class="card border-0 shadow-sm rounded-4 p-3 mb-4"><div class="row g-2"><div class="col-md-5"><input name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama pihak atau referensi"></div><div class="col-md-3"><select name="direction" class="form-select"><option value="">Semua jenis</option><option value="payable" @selected(request('direction') === 'payable')>Utang</option><option value="receivable" @selected(request('direction') === 'receivable')>Piutang</option></select></div><div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status</option><option value="open" @selected(request('status') === 'open')>Belum dibayar</option><option value="partial" @selected(request('status') === 'partial')>Sebagian</option><option value="paid" @selected(request('status') === 'paid')>Lunas</option><option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option></select></div><div class="col-md-1"><button class="btn btn-outline-dark w-100">Cari</button></div></div></form>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Pihak</th><th>Jenis</th><th>Jatuh tempo</th><th>Status</th><th class="text-end">Sisa</th><th class="text-end px-4">Aksi</th></tr></thead><tbody>
    @forelse($debts as $debt)
        <tr><td class="px-4"><strong>{{ $debt->party_name }}</strong><div class="small text-muted">{{ $debt->reference ?: ucfirst($debt->party_type) }}</div></td><td><span class="badge rounded-pill {{ $debt->direction === 'payable' ? 'text-bg-danger' : 'text-bg-success' }}">{{ $debt->direction_label }}</span></td><td>{{ $debt->due_date?->format('d M Y') ?: '-' }}</td><td>{{ ucfirst($debt->status) }}</td><td class="text-end">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</td><td class="text-end px-4"><a href="{{ route('debts.show', $debt) }}" class="btn btn-sm btn-light border rounded-pill">Detail</a></td></tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-5">Belum ada catatan utang/piutang.</td></tr>
    @endforelse
    </tbody></table></div><div class="p-3">{{ $debts->links() }}</div></div>
</div>
@endsection
