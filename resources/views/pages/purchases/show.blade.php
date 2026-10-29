@extends('layouts.app')
@section('title', 'Detail Pembelian | Skincare POS')
@section('content')
<div class="container py-4"><div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="font-serif fw-bold mb-1">Detail Pembelian #{{ $purchase->id }}</h3><p class="text-muted mb-0">{{ $purchase->purchase_date->format('d M Y') }} · {{ $purchase->supplier?->name ?: 'Belanja mandiri' }}</p></div><a href="{{ route('purchases.index') }}" class="btn btn-light border rounded-pill">Kembali</a></div>
@include('partials.flash')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Produk</th><th>Jumlah</th><th>Harga modal</th><th class="text-end px-4">Subtotal</th></tr></thead><tbody>@foreach($purchase->items as $item)<tr><td class="px-4">{{ $item->product->name }}</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->unit_cost,0,',','.') }}</td><td class="text-end px-4">Rp {{ number_format($item->subtotal,0,',','.') }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end px-4">Rp {{ number_format($purchase->total_amount,0,',','.') }}</th></tr></tfoot></table></div></div>
@if($purchase->status === 'draft')<form action="{{ route('purchases.receive', $purchase) }}" method="POST" class="d-inline" data-confirm-delete data-confirm-title="Terima pembelian?" data-confirm-text="Stok akan ditambahkan sekarang.">@csrf<button class="btn btn-success rounded-pill">Terima Pembelian</button></form>@endif
@if($purchase->status === 'received')<form action="{{ route('purchases.cancel', $purchase) }}" method="POST" class="mt-3" data-confirm-delete data-confirm-title="Batalkan pembelian?" data-confirm-text="Stok akan dikurangi kembali.">@csrf<button class="btn btn-outline-danger rounded-pill">Batalkan Pembelian</button></form>@endif
</div>
@endsection
