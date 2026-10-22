@extends('layouts.app')

@section('content')
@php
    $appSetting = \App\Models\Setting::first();
    $contactPhone = $appSetting->store_phone ?? null;
    $contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone ?? '');
@endphp

<style>
    html {
        scroll-behavior: smooth;
    }
    #features,
    #about,
    #contact {
        scroll-margin-top: 6rem;
    }
    .hero-section {
        padding: 5rem 0 7rem;
        position: relative;
        overflow: hidden;
    }
    .bg-circle {
        position: absolute;
        width: 650px;
        height: 650px;
        background: linear-gradient(135deg, rgba(199,139,125,0.05) 0%, rgba(199,139,125,0.15) 100%);
        border-radius: 50%;
        top: -50px;
        right: -100px;
        z-index: -1;
    }
    .hero-title {
        font-size: clamp(2.75rem, 5vw, 4.5rem);
        line-height: 1.1;
        font-weight: 500;
        letter-spacing: -1px;
    }
    .landing-image-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background-color: #f0e9e4;
    }
    .landing-image-frame img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    .btn-hero {
        background-color: var(--primary-color);
        color: #fff;
        border-radius: 50rem;
        padding: 0.9rem 2.8rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-hero:hover {
        background-color: var(--primary-hover);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(199, 139, 125, 0.2);
    }
    .btn-hero-outline {
        background-color: transparent;
        color: var(--text-dark);
        border: 1px solid var(--border-color);
        border-radius: 50rem;
        padding: 0.9rem 2.8rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    .btn-hero-outline:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    .feature-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 1.5rem;
        padding: clamp(1.5rem, 4vw, 3rem);
        box-shadow: 0 15px 40px rgba(59,51,49,0.04);
        margin-top: -3rem;
        position: relative;
        z-index: 10;
    }
    @media (max-width: 767.98px) {
        .hero-section {
            padding: 3.5rem 0 5rem;
        }
        .hero-benefits {
            gap: 1rem 1.5rem !important;
        }
    }
</style>

<!-- HERO SECTION -->
<div class="hero-section">
    <div class="bg-circle"></div>
    <div class="container px-4 px-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 pe-lg-5">
                <p class="fw-bold mb-3" style="color: var(--primary-color); letter-spacing: 2px; font-size: 0.85rem; text-transform: uppercase;">
                    Manajemen Toko Profesional
                </p>
                
                <h1 class="hero-title font-serif mb-4 text-dark">
                    {{ $appSetting->landing_title ?? 'Solusi Cerdas' }} <br>
                    <span style="color: var(--primary-color); font-style: italic;">{{ $appSetting->landing_text_highlight ?? 'Kelola Toko Skincare' }}</span>
                </h1>
                
                <p class="mb-5 text-muted" style="font-size: 1.15rem; line-height: 1.8;">
                    {{ $appSetting->landing_description ?? 'Sistem kasir dan manajemen inventori untuk membantu toko skincare mencatat transaksi, memantau stok, dan melihat ringkasan penjualan.' }}
                </p>
                
                <div class="d-flex flex-wrap gap-3 mb-5">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('dashboard.index') }}" class="btn-hero text-decoration-none">Buka Dashboard</a>
                        @else
                            <a href="{{ route('pos.index') }}" class="btn-hero text-decoration-none">Buka Kasir</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-hero text-decoration-none">Masuk Sistem</a>
                    @endauth
                    <a href="{{ route('landing') . '#features' }}" class="btn-hero-outline text-decoration-none">Pelajari Fitur</a>
                </div>

                <div class="d-flex flex-wrap gap-5 hero-benefits">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-check-circle" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                        <span class="small fw-bold text-dark text-uppercase" style="letter-spacing: 1px;">Cepat</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-check-circle" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                        <span class="small fw-bold text-dark text-uppercase" style="letter-spacing: 1px;">Akurat</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-check-circle" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                        <span class="small fw-bold text-dark text-uppercase" style="letter-spacing: 1px;">Aman</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                @if($appSetting && $appSetting->landing_image)
                    <div class="landing-image-frame rounded-4 shadow">
                        <img src="{{ asset('storage/' . $appSetting->landing_image) }}" class="hero-image" alt="Tampilan {{ $appSetting->store_name ?? 'toko skincare' }}" fetchpriority="high">
                    </div>
                @else
                    <div class="landing-image-frame rounded-4 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="fas fa-spa" style="font-size: 6rem; color: #fff;"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SECTION 1: FITUR -->
<div class="container px-4 px-lg-5 mb-5 pb-5" id="features">
    <div class="feature-box">
        <div class="row g-4 text-center text-md-start">
            <div class="col-md-3 border-end-md px-lg-4">
                <i class="fas fa-cash-register mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Penjualan Instan</h6>
                <p class="small mb-0 text-muted">Catat transaksi penjualan dengan alur kasir yang ringkas untuk {{ $appSetting->store_name ?? 'toko Anda' }}.</p>
            </div>
            <div class="col-md-3 border-end-md px-lg-4">
                <i class="fas fa-box-open mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Pantau Stok</h6>
                <p class="small mb-0 text-muted">Pantau sisa barang dan kenali produk yang perlu segera di-restock.</p>
            </div>
            <div class="col-md-3 border-end-md px-lg-4">
                <i class="fas fa-shield-alt mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Hak Akses Aman</h6>
                <p class="small mb-0 text-muted">Menu admin dan kasir mengikuti hak akses akun masing-masing.</p>
            </div>
            <div class="col-md-3 px-lg-4">
                <i class="fas fa-chart-pie mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Laporan Akurat</h6>
                <p class="small mb-0 text-muted">Lihat ringkasan omzet dan laba kotor berdasarkan data transaksi yang tersimpan.</p>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 2: TENTANG KAMI -->
<div class="container px-4 px-lg-5 mb-5 pb-5" id="about">
    <div class="row align-items-center g-5">
        <div class="col-lg-6 order-2 order-lg-1">
            <h2 class="font-serif fw-bold text-dark mb-4">Mendukung Pertumbuhan Bisnis Kecantikan Anda</h2>
            <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                <strong>{{ $appSetting->store_name ?? 'Skincare POS' }}</strong> membantu toko skincare mencatat penjualan dan mengelola stok produk dalam satu alur kerja.
            </p>
            <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                Data transaksi menyimpan harga dan HPP saat penjualan dibuat, sehingga ringkasan penjualan dapat ditelusuri kembali dari riwayat transaksi.
            </p>
            <div class="d-flex gap-4 mt-4">
                <div class="text-center">
                    <h3 class="font-serif fw-bold mb-0" style="color: var(--primary-color);"><i class="fas fa-cash-register" aria-hidden="true"></i></h3>
                    <span class="small fw-bold text-muted text-uppercase">Kasir</span>
                </div>
                <div class="text-center">
                    <h3 class="font-serif fw-bold mb-0" style="color: var(--primary-color);"><i class="fas fa-boxes-stacked" aria-hidden="true"></i></h3>
                    <span class="small fw-bold text-muted text-uppercase">Stok</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2">
            <div class="position-relative">
                <div class="rounded-4" aria-hidden="true" style="background-color: var(--primary-color); opacity: 0.15; width: 100%; height: 100%; position: absolute; top: 20px; left: -20px; z-index: 1;"></div>
                <div class="landing-image-frame rounded-4 d-flex align-items-center justify-content-center position-relative shadow-sm" style="border: 1px solid var(--border-color); z-index: 2;">
                    @if($appSetting?->about_image)
                        <img src="{{ asset('storage/' . $appSetting->about_image) }}" class="about-image" alt="Tentang {{ $appSetting->store_name ?? 'Skincare POS' }}" loading="lazy" decoding="async">
                    @else
                        <i class="fas fa-pump-soap" aria-hidden="true" style="font-size: 8rem; color: var(--primary-color); opacity: 0.3;"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 3: KONTAK -->
<div class="container px-4 px-lg-5 mb-5 pb-3" id="contact">
    <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background-color: var(--card-bg);">
        <div class="row g-0">
            <!-- Kontak toko mengikuti data pada Pengaturan -->
            <div class="col-lg-5 text-white p-5 d-flex flex-column justify-content-center" style="background-color: var(--primary-color);">
                <h3 class="font-serif fw-bold mb-4">Hubungi Toko</h3>
                <p class="mb-5 opacity-75" style="line-height: 1.8;">Informasi kontak di bagian ini mengikuti data toko yang diatur oleh admin.</p>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <i class="fas fa-phone mt-1 fs-5" aria-hidden="true"></i>
                    <div>
                        <span class="d-block fw-bold small text-uppercase opacity-75 mb-1">Telepon</span>
                        @if($contactPhone)
                            <a class="text-white" href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                        @else
                            <span>Nomor telepon belum diatur.</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-map-marker-alt mt-1 fs-5" aria-hidden="true"></i>
                    <div>
                        <span class="d-block fw-bold small text-uppercase opacity-75 mb-1">Alamat Toko</span>
                        <span>{{ $appSetting->store_address ?? 'Alamat toko belum diatur.' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Aksi sesuai akses pengguna -->
            <div class="col-lg-7 p-5">
                <h4 class="font-serif fw-bold text-dark mb-3">Butuh bantuan?</h4>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.8;">
                    Hubungi toko melalui nomor telepon yang tercantum. Untuk menggunakan kasir atau mengelola toko, masuk dengan akun yang sudah dibuat admin.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @if($contactPhone)
                        <a href="tel:{{ $contactPhoneHref }}" class="btn btn-dark rounded-pill px-4">
                            <i class="fas fa-phone me-2" aria-hidden="true"></i>Telepon Toko
                        </a>
                    @endif
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('settings.index') }}" class="btn btn-light border rounded-pill px-4">Pengaturan Toko</a>
                        @else
                            <a href="{{ route('pos.index') }}" class="btn btn-light border rounded-pill px-4">Buka Kasir</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light border rounded-pill px-4">Masuk Sistem</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="py-4 border-top text-center" style="border-color: var(--border-color) !important;">
    <div class="container">
        <p class="mb-0 text-muted small fw-medium">&copy; {{ date('Y') }} {{ $appSetting->store_name ?? 'Skincare POS' }}. Hak Cipta Dilindungi.</p>
    </div>
</footer>
@endsection
