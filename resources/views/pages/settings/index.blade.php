@extends('layouts.app')

@section('title', 'Pengaturan Sistem | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <!-- HEADER HALAMAN STANDAR -->
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Pengaturan Sistem</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola identitas, tampilan landing page, dan format struk.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- NAV TABS ELEGAN -->
    <ul class="nav nav-pills gap-2 mb-4" id="settingTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-medium px-4 rounded-pill border" data-bs-toggle="pill" data-bs-target="#generalTab" type="button" style="color: var(--text-dark);">
                Identitas Toko
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium px-4 rounded-pill border" data-bs-toggle="pill" data-bs-target="#landingTab" type="button" style="color: var(--text-dark);">
                Visual Landing Page
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium px-4 rounded-pill border" data-bs-toggle="pill" data-bs-target="#receiptTab" type="button" style="color: var(--text-dark);">
                Format Struk
            </button>
        </li>
    </ul>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="tab-content" id="settingTabsContent">
                
                <!-- TAB 1: GENERAL -->
                <div class="tab-pane fade show active" id="generalTab">
                    <h5 class="font-serif fw-bold text-dark mb-4">Identitas Umum & Branding</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Nama Toko / Brand</label>
                            <input type="text" name="store_name" class="form-control bg-light" value="{{ $setting->store_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Warna Tema Utama</label>
                            <div class="d-flex align-items-center gap-3 bg-light border rounded-3 px-3 py-1">
                                <input type="color" name="primary_color" class="form-control form-control-color border-0 bg-transparent p-0" value="{{ $setting->primary_color ?? '#c78b7d' }}" style="width: 40px;">
                                <span class="small text-muted">Sesuaikan dengan brand Anda</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold small text-muted mb-2">Logo Toko (Opsional)</label>
                            <div class="d-flex align-items-center gap-3">
                                @if($setting->logo)
                                    <img src="{{ asset('storage/' . $setting->logo) }}" class="rounded-circle border" width="50" height="50" style="object-fit: cover;">
                                @endif
                                <input type="file" name="logo" class="form-control bg-light w-auto flex-grow-1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: LANDING PAGE -->
                <div class="tab-pane fade" id="landingTab">
                    <h5 class="font-serif fw-bold text-dark mb-4">Konten Utama Landing Page</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Judul Utama</label>
                            <input type="text" name="landing_title" class="form-control bg-light" value="{{ $setting->landing_title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Teks Sorotan (Warna Tema)</label>
                            <input type="text" name="landing_text_highlight" class="form-control bg-light" value="{{ $setting->landing_text_highlight }}" required>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold small text-muted mb-2">Deskripsi Singkat</label>
                            <textarea name="landing_description" class="form-control bg-light" rows="3" required>{{ $setting->landing_description }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold small text-muted mb-2">Gambar Banner / Ilustrasi</label>
                            <div class="d-flex align-items-center gap-3">
                                @if($setting->landing_image)
                                    <img src="{{ asset('storage/' . $setting->landing_image) }}" class="rounded-3 border" width="80" height="80" style="object-fit: cover;">
                                @endif
                                <input type="file" name="landing_image" class="form-control bg-light w-auto flex-grow-1" accept="image/*">
                            </div>
                        </div>
                        <!-- Font Family Dropdown disembunyikan agar dipaksa pakai Serif Premium Global -->
                        <input type="hidden" name="font_family" value="Playfair Display">
                    </div>
                </div>

                <!-- TAB 3: STRUK -->
                <div class="tab-pane fade" id="receiptTab">
                    <h5 class="font-serif fw-bold text-dark mb-4">Informasi Struk Pembelian</h5>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="fw-bold small text-muted mb-2">Alamat Lengkap Toko</label>
                            <textarea name="store_address" class="form-control bg-light" rows="2" required>{{ $setting->store_address }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="store_phone" class="form-control bg-light" value="{{ $setting->store_phone }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Pesan Penutup (Footer)</label>
                            <input type="text" name="receipt_footer" class="form-control bg-light" value="{{ $setting->receipt_footer }}">
                        </div>
                    </div>
                </div>

            </div>

            <hr class="my-5" style="border-color: var(--border-color);">
            <div class="text-end">
                <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow-sm">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tambahkan sedikit JS agar Tab Active mewarisi warna primer -->
<style>
    .nav-pills .nav-link.active {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
</style>
@endsection
