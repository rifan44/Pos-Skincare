@extends('layouts.app')

@section('title', 'Inventori | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Inventori</h3>
            <p class="text-muted mb-0">Kelola pemasok, barang masuk, dan riwayat pergerakan stok.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('suppliers.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-truck-field"></i></span>
                <span class="management-card-content">
                    <strong>Supplier</strong>
                    <small>Kelola data pemasok dan status supplier.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('purchases.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-cart-flatbed"></i></span>
                <span class="management-card-content">
                    <strong>Pembelian / Belanja</strong>
                    <small>Catat barang masuk dari supplier atau belanja mandiri.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('stock-movements.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-right-left"></i></span>
                <span class="management-card-content">
                    <strong>Arus Stok</strong>
                    <small>Lihat ledger stok masuk dan keluar secara read-only.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('stock-adjustments.create') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-sliders"></i></span>
                <span class="management-card-content">
                    <strong>Penyesuaian Stok</strong>
                    <small>Catat stok opname, barang rusak, kedaluwarsa, atau koreksi dengan alasan.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
    </div>
</div>

@include('pages.management.partials.card-styles')
@endsection
