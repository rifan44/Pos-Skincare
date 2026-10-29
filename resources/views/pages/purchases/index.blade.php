@extends('layouts.app')
@section('title', 'Pembelian | Skincare POS')
@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="font-serif fw-bold mb-1">Pembelian / Belanja</h3><p class="text-muted mb-0">Setiap dokumen yang diterima menambah stok dan membuat ledger.</p></div><a href="{{ route('purchases.create') }}" class="btn btn-dark rounded-pill"><i class="fas fa-plus me-2"></i>Catat Pembelian</a></div>
    @include('partials.flash')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="px-4">Tanggal</th><th>Sumber</th><th>Supplier</th><th>Status</th><th class="text-end">Total</th><th class="text-end px-4"></th></tr></thead>
        <tbody>@forelse($purchases as $purchase)<tr>
            <td class="px-4">{{ $purchase->purchase_date->format('d M Y') }}<div class="small text-muted">{{ $purchase->invoice_number ?: 'Tanpa nomor invoice' }}</div></td>
            <td>{{ $purchase->source === 'supplier' ? 'Supplier' : 'Belanja mandiri' }}</td><td>{{ $purchase->supplier?->name ?: '-' }}</td>
            <td><span class="badge rounded-pill {{ $purchase->status === 'received' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($purchase->status) }}</span></td>
            <td class="text-end">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
            <td class="text-end px-4"><a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-light border rounded-pill">Detail</a></td>
        </tr>@empty<tr><td colspan="6" class="text-center text-muted py-5">Belum ada pembelian.</td></tr>@endforelse</tbody>
    </table></div><div class="p-3">{{ $purchases->links() }}</div></div>
</div>
@endsection
