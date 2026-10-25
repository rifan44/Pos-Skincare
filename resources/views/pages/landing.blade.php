@extends('layouts.app')

@section('content')
@php
    $appSetting = \App\Models\Setting::first();
    $contactPhone = trim((string) ($appSetting?->store_phone ?? ''));
    $whatsappNumber = preg_replace('/\D+/', '', $contactPhone);

    if (str_starts_with($whatsappNumber, '00')) {
        $whatsappNumber = substr($whatsappNumber, 2);
    } elseif (!str_starts_with($contactPhone, '+')) {
        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62'.substr($whatsappNumber, 1);
        } elseif (!str_starts_with($whatsappNumber, '62')) {
            $whatsappNumber = '62'.$whatsappNumber;
        }
    }

    $whatsappUrl = preg_match('/^[1-9]\d{5,14}$/', $whatsappNumber)
        ? 'https://wa.me/'.$whatsappNumber
        : null;
    $socialLinks = $appSetting?->social_links ?? [];
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
    .hero-image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
    .hero-image-container img {
        display: block;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
    }
    .about-panel {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        border: 1px solid var(--border-color);
        background: linear-gradient(135deg, var(--card-bg) 0%, var(--surface-alt) 100%);
        box-shadow: 0 1rem 3rem rgba(59, 51, 49, 0.07);
    }
    .about-panel::before {
        content: '';
        position: absolute;
        z-index: 0;
        top: -8rem;
        right: -5rem;
        width: 22rem;
        height: 22rem;
        border-radius: 50%;
        background: rgba(199, 139, 125, 0.12);
        pointer-events: none;
    }
    .about-panel > .row {
        position: relative;
        z-index: 1;
    }
    .about-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.55rem 0.9rem;
        border-radius: 50rem;
        background: var(--surface-alt);
        color: var(--text-dark);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .about-eyebrow i {
        color: var(--primary-color);
    }
    .about-highlight {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 1.1rem;
        background: var(--card-bg);
    }
    .about-highlight-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.65rem;
        height: 2.65rem;
        border-radius: 0.9rem;
        background: var(--surface-alt);
        color: var(--primary-color);
    }
    html[data-theme="dark"] .about-panel {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.2);
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
                    <div class="hero-image-container">
                        <img src="{{ asset('storage/' . $appSetting->landing_image) }}" class="hero-image rounded-4 shadow" alt="Tampilan {{ $appSetting->store_name ?? 'toko skincare' }}" fetchpriority="high">
                    </div>
                @else
                    <div class="rounded-4 d-flex align-items-center justify-content-center w-100" style="background-color: #f0e9e4; min-height: 320px;">
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
    <div class="about-panel rounded-5 p-4 p-lg-5">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7">
                <span class="about-eyebrow"><i class="fas fa-spa" aria-hidden="true"></i>Tentang {{ $appSetting->store_name ?? 'Skincare POS' }}</span>
                <h2 class="font-serif fw-bold text-dark mt-4 mb-3">Operasional toko skincare, tertata dalam satu sistem</h2>
                <p class="text-muted mb-3" style="font-size: 1.05rem; line-height: 1.8;">
                    <strong>{{ $appSetting->store_name ?? 'Skincare POS' }}</strong> menyatukan data produk, transaksi kasir, dan pemantauan stok agar pekerjaan harian lebih mudah ditinjau.
                </p>
                <p class="text-muted mb-0" style="line-height: 1.8;">
                    Kasir dapat menemukan produk berdasarkan jenis dan merek, sementara stok diperbarui setelah checkout berhasil dan admin dapat meninjau aktivitas penjualan.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="about-highlight">
                            <span class="about-highlight-icon mb-3"><i class="fas fa-tags" aria-hidden="true"></i></span>
                            <h6 class="fw-bold text-dark mb-1">Jenis & Merek</h6>
                            <p class="small text-muted mb-0">Temukan produk lewat kelompok yang sesuai.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-highlight">
                            <span class="about-highlight-icon mb-3"><i class="fas fa-cash-register" aria-hidden="true"></i></span>
                            <h6 class="fw-bold text-dark mb-1">Kasir Praktis</h6>
                            <p class="small text-muted mb-0">Catat transaksi dari satu halaman.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-highlight">
                            <span class="about-highlight-icon mb-3"><i class="fas fa-boxes-stacked" aria-hidden="true"></i></span>
                            <h6 class="fw-bold text-dark mb-1">Stok Terpantau</h6>
                            <p class="small text-muted mb-0">Perubahan stok mengikuti checkout berhasil.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-highlight">
                            <span class="about-highlight-icon mb-3"><i class="fas fa-chart-pie" aria-hidden="true"></i></span>
                            <h6 class="fw-bold text-dark mb-1">Laporan Toko</h6>
                            <p class="small text-muted mb-0">Bantu admin meninjau penjualan toko.</p>
                        </div>
                    </div>
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
            <div class="col-lg-5 text-white p-4 p-lg-5 d-flex flex-column justify-content-center" style="background-color: var(--primary-color);">
                <h3 class="font-serif fw-bold mb-4">Hubungi Toko</h3>
                <p class="mb-5 opacity-75" style="line-height: 1.8;">Informasi kontak di bagian ini mengikuti data toko yang diatur oleh admin.</p>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <i class="fa-brands fa-whatsapp mt-1 fs-5" aria-hidden="true"></i>
                    <div>
                        <span class="d-block fw-bold small text-uppercase opacity-75 mb-1">WhatsApp</span>
                        @if($whatsappUrl)
                            <a class="text-white" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">{{ $contactPhone }}</a>
                        @elseif($contactPhone)
                            <span>{{ $contactPhone }} (format nomor WhatsApp tidak valid)</span>
                        @else
                            <span>Nomor WhatsApp belum diatur.</span>
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
            <div class="col-lg-7 p-4 p-lg-5">
                <h4 class="font-serif fw-bold text-dark mb-3">Butuh bantuan?</h4>
                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.8;">
                    Hubungi toko melalui WhatsApp. Untuk menggunakan kasir atau mengelola toko, masuk dengan akun yang sudah dibuat admin.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @if($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" class="btn btn-dark rounded-pill px-4" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-whatsapp me-2" aria-hidden="true"></i>Chat WhatsApp
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

                @if(count($socialLinks))
                    <div class="mt-4">
                        <span class="d-block small fw-bold text-muted text-uppercase mb-2">Ikuti Media Sosial Kami</span>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($socialLinks as $socialLink)
                                @php
                                    $socialName = $socialLink['name'] ?? 'Media Sosial';
                                    $socialIcon = match (strtolower($socialName)) {
                                        'instagram' => 'fa-brands fa-instagram',
                                        'tiktok', 'tik tok' => 'fa-brands fa-tiktok',
                                        'facebook' => 'fa-brands fa-facebook',
                                        'youtube' => 'fa-brands fa-youtube',
                                        default => 'fas fa-link',
                                    };
                                @endphp
                                <a href="{{ $socialLink['url'] }}" class="btn btn-outline-dark rounded-pill px-3" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialName }}">
                                    <i class="{{ $socialIcon }} me-2" aria-hidden="true"></i>{{ $socialName }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
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
