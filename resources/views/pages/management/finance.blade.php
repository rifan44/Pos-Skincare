@extends('layouts.app')

@section('title', 'Keuangan | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Keuangan</h3>
            <p class="text-muted mb-0">Pantau laporan penjualan serta kewajiban dan tagihan toko.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('reports.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-chart-line"></i></span>
                <span class="management-card-content">
                    <strong>Laporan Penjualan</strong>
                    <small>Tinjau omzet, diskon, refund, HPP, dan laba kotor.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('debts.index') }}" class="management-card">
                <span class="management-card-icon"><i class="fas fa-hand-holding-dollar"></i></span>
                <span class="management-card-content">
                    <strong>Utang & Piutang</strong>
                    <small>Catat kewajiban supplier dan tagihan customer beserta pembayarannya.</small>
                </span>
                <i class="fas fa-arrow-right management-card-arrow"></i>
            </a>
        </div>
    </div>
</div>

@include('pages.management.partials.card-styles')
@endsection
