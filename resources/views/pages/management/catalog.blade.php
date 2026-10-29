@extends('layouts.app')

@section('title', 'Katalog Produk | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Katalog Produk</h3>
            <p class="text-muted mb-0">Kelola produk dan pengelompokan katalog dari satu halaman.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('products.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-box-open"></i></span>
                <span class="management-card-content">
                    <strong>Produk</strong>
                    <small>Tambah, ubah, atur harga, stok, dan data produk.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('categories.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-tags"></i></span>
                <span class="management-card-content">
                    <strong>Kategori</strong>
                    <small>Kelola kategori jenis produk dan merek.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('customers.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-user-group"></i></span>
                <span class="management-card-content">
                    <strong>Customer & Reseller</strong>
                    <small>Simpan identitas customer dan reseller untuk transaksi.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
    </div>
</div>

@include('pages.management.partials.card-styles')
@endsection
