@extends('layouts.app')

@section('content')
@php
    $appSetting = \App\Models\Setting::first();
    $primaryColor = $appSetting->primary_color ?? '#c78b7d';
@endphp

<style>
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
        font-size: 4.5rem;
        line-height: 1.1;
        font-weight: 500;
        letter-spacing: -1px;
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
        padding: 3rem;
        box-shadow: 0 15px 40px rgba(59,51,49,0.04);
        margin-top: -3rem;
        position: relative;
        z-index: 10;
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
                    {{ $appSetting->landing_description ?? 'Sistem kasir dan manajemen inventori modern yang dirancang khusus untuk bisnis kecantikan ranbeautee. Pantau stok dan layani pelanggan dengan elegan.' }}
                </p>
                
                <div class="d-flex flex-wrap gap-3 mb-5">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn-hero text-decoration-none">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero text-decoration-none">Masuk Sistem</a>
                    @endauth
                    <a href="#features" class="btn-hero-outline text-decoration-none">Pelajari Fitur</a>
                </div>

                <div class="d-flex gap-5">
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
                    <img src="{{ asset('storage/' . $appSetting->landing_image) }}" class="img-fluid rounded-4 shadow" style="object-fit: cover; max-height: 550px; width: 100%;" alt="Produk">
                @else
                    <div class="rounded-4 d-flex align-items-center justify-content-center w-100" style="background-color: #f0e9e4; height: 500px;">
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
                <p class="small mb-0 text-muted">Proses kasir super cepat tanpa antrean panjang di toko ranbeautee Anda.</p>
            </div>
            <div class="col-md-3 border-end-md px-lg-4">
                <i class="fas fa-box-open mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Pantau Stok</h6>
                <p class="small mb-0 text-muted">Ketahui sisa barang secara real-time dan terhindar dari kehabisan stok.</p>
            </div>
            <div class="col-md-3 border-end-md px-lg-4">
                <i class="fas fa-shield-alt mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Hak Akses Aman</h6>
                <p class="small mb-0 text-muted">Akses terpisah dan aman antara pemilik toko (Admin) dan Kasir.</p>
            </div>
            <div class="col-md-3 px-lg-4">
                <i class="fas fa-chart-pie mb-3 fs-3" style="color: var(--primary-color);"></i>
                <h6 class="font-serif fw-bold text-dark mb-2 fs-5">Laporan Akurat</h6>
                <p class="small mb-0 text-muted">Hitung omzet dan estimasi laba bersih secara otomatis setiap hari.</p>
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
                Berawal dari pemahaman akan dinamika industri perawatan kulit, <strong>Skincare POS</strong> hadir sebagai solusi perangkat lunak kasir (POS) yang dirancang khusus untuk memenuhi standar operasional klinik kecantikan dan toko skincare modern.
            </p>
            <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                Misi kami adalah menyederhanakan pencatatan transaksi dan manajemen stok Anda, sehingga Anda dapat lebih fokus pada hal yang paling penting: <em>memberikan pelayanan terbaik bagi pelanggan setia Anda.</em>
            </p>
            <div class="d-flex gap-4 mt-4">
                <div class="text-center">
                    <h3 class="font-serif fw-bold mb-0" style="color: var(--primary-color);">100%</h3>
                    <span class="small fw-bold text-muted text-uppercase">Akurat</span>
                </div>
                <div class="text-center">
                    <h3 class="font-serif fw-bold mb-0" style="color: var(--primary-color);">24/7</h3>
                    <span class="small fw-bold text-muted text-uppercase">Pantauan</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2">
            <div class="position-relative">
                <div class="rounded-4" style="background-color: var(--primary-color); opacity: 0.15; width: 100%; height: 100%; position: absolute; top: 20px; left: -20px; z-index: 1;"></div>
                <!-- Placeholder estetik berkelas jika gambar asli belum ada -->
                <div class="rounded-4 d-flex align-items-center justify-content-center position-relative shadow-sm" style="background-color: var(--card-bg); border: 1px solid var(--border-color); height: 400px; z-index: 2;">
                    <i class="fas fa-pump-soap" style="font-size: 8rem; color: var(--primary-color); opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 3: KONTAK -->
<div class="container px-4 px-lg-5 mb-5 pb-3" id="contact">
    <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background-color: var(--card-bg);">
        <div class="row g-0">
            <!-- Info Kontak (Warna Tema) -->
            <div class="col-lg-5 text-white p-5 d-flex flex-column justify-content-center" style="background-color: var(--primary-color);">
                <h3 class="font-serif fw-bold mb-4">Hubungi Kami</h3>
                <p class="mb-5 opacity-75" style="line-height: 1.8;">Tim dukungan teknis Skincare POS selalu siap membantu Anda mengatur sistem kasir untuk toko Anda.</p>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <i class="fas fa-envelope mt-1 fs-5"></i>
                    <div>
                        <span class="d-block fw-bold small text-uppercase opacity-75 mb-1">Email</span>
                        <span>halo@skincarepos.com</span>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3 mb-4">
                    <i class="fas fa-phone mt-1 fs-5"></i>
                    <div>
                        <span class="d-block fw-bold small text-uppercase opacity-75 mb-1">Telepon / WhatsApp</span>
                        <span>+62 811 2345 6789</span>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-map-marker-alt mt-1 fs-5"></i>
                    <div>
                        <span class="d-block fw-bold small text-uppercase opacity-75 mb-1">Kantor Pusat</span>
                        <span>Sukabumi, Jawa Barat<br>Indonesia</span>
                    </div>
                </div>
            </div>
            
            <!-- Form Kirim Pesan -->
            <div class="col-lg-7 p-5">
                <h4 class="font-serif fw-bold text-dark mb-4">Kirim Pesan</h4>
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">Nama Lengkap</label>
                            <input type="text" class="form-control bg-light" placeholder="Nama Anda" style="border-radius: 0.5rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">Alamat Email</label>
                            <input type="email" class="form-control bg-light" placeholder="Email Anda" style="border-radius: 0.5rem;">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-2">Topik Pesan</label>
                            <select class="form-select bg-light" style="border-radius: 0.5rem;">
                                <option>Pertanyaan Seputar Aplikasi</option>
                                <option>Bantuan Teknis Kasir</option>
                                <option>Kerjasama Bisnis</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-2">Pesan Anda</label>
                            <textarea class="form-control bg-light" rows="4" placeholder="Tuliskan detail pertanyaan Anda di sini..." style="border-radius: 0.5rem;"></textarea>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn text-white rounded-pill px-5 py-2 fw-bold shadow-sm" style="background-color: var(--text-dark);">
                                Kirim Pesan <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="py-4 border-top text-center" style="border-color: var(--border-color) !important;">
    <div class="container">
        <p class="mb-0 text-muted small fw-medium">&copy; {{ date('Y') }} {{ $appSetting->store_name ?? 'ranbeautee' }}. Hak Cipta Dilindungi.</p>
    </div>
</footer>
@endsection