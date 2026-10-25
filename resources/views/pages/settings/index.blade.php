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
                                <input type="file" name="logo" class="form-control bg-light w-auto flex-grow-1 setting-logo-input" accept="image/*">
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
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted mb-2">Gambar Utama / Hero</label>
                            @if($setting->landing_image)
                                <div class="landing-image-preview rounded-3 border mb-2">
                                    <img src="{{ asset('storage/' . $setting->landing_image) }}" alt="Pratinjau gambar utama landing page" data-landing-image-preview>
                                </div>
                            @endif
                            <input type="file" id="landingImageInput" name="landing_image" class="form-control bg-light" accept="image/jpeg,image/png,image/webp,image/gif,image/bmp">
                            <div class="form-text">JPG, PNG, WebP, GIF, atau BMP, maksimal 8 MB. Gunakan gambar sumber dengan sisi terpanjang minimal 1400 px (ideal 1600 px atau lebih); untuk rasio 3:2, sekitar 1600 × 1067 px. Gambar ditampilkan proporsional dan tidak di-upscale; resolusi sumber rendah dapat terlihat pecah, terutama di layar berkepadatan tinggi.</div>
                            <div class="form-text" id="landingImageResolution" aria-live="polite">
                                @if($setting->landing_image)
                                    Resolusi gambar saat ini akan ditampilkan setelah dimuat.
                                @else
                                    Pilih gambar untuk memeriksa resolusinya sebelum menyimpan.
                                @endif
                            </div>
                        </div>

                        @php($socialLinks = old('social_links', $setting->social_links ?? []))
                        <div class="col-12">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <label class="fw-bold small text-muted mb-0">Media Sosial di Bagian Kontak</label>
                                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill" id="addSocialLink">
                                    <i class="fas fa-plus me-1" aria-hidden="true"></i>Tambah Media Sosial
                                </button>
                            </div>
                            <p class="form-text mt-0">Tambahkan nama platform dan tautan profil, misalnya Instagram atau TikTok. Maksimal 10 tautan.</p>
                            <div id="socialLinksList" class="d-grid gap-3">
                                @forelse($socialLinks as $index => $socialLink)
                                    <div class="row g-2 align-items-end social-link-row" data-social-link-row>
                                        <div class="col-md-5">
                                            <label class="form-label small" for="social-links-{{ $index }}-name">Nama platform</label>
                                            <input type="text" id="social-links-{{ $index }}-name" name="social_links[{{ $index }}][name]" class="form-control bg-light" value="{{ $socialLink['name'] ?? '' }}" maxlength="40" placeholder="Instagram, TikTok, ...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small" for="social-links-{{ $index }}-url">Tautan profil</label>
                                            <input type="url" id="social-links-{{ $index }}-url" name="social_links[{{ $index }}][url]" class="form-control bg-light" value="{{ $socialLink['url'] ?? '' }}" maxlength="255" placeholder="https://...">
                                        </div>
                                        <div class="col-md-1 d-grid">
                                            <button type="button" class="btn btn-outline-danger rounded-pill" data-remove-social-link aria-label="Hapus tautan media sosial">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="small text-muted mb-0" data-social-links-empty>Belum ada tautan media sosial.</p>
                                @endforelse
                            </div>
                        </div>
                        <!-- Font Family Dropdown disembunyikan agar dipaksa pakai Serif Premium Global -->
                        <input type="hidden" name="font_family" value="Playfair Display">
                    </div>

                    <template id="socialLinkTemplate">
                        <div class="row g-2 align-items-end social-link-row" data-social-link-row>
                            <div class="col-md-5">
                                <label class="form-label small" for="social-links-__INDEX__-name">Nama platform</label>
                                <input type="text" id="social-links-__INDEX__-name" name="social_links[__INDEX__][name]" class="form-control bg-light" maxlength="40" placeholder="Instagram, TikTok, ...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small" for="social-links-__INDEX__-url">Tautan profil</label>
                                <input type="url" id="social-links-__INDEX__-url" name="social_links[__INDEX__][url]" class="form-control bg-light" maxlength="255" placeholder="https://...">
                            </div>
                            <div class="col-md-1 d-grid">
                                <button type="button" class="btn btn-outline-danger rounded-pill" data-remove-social-link aria-label="Hapus tautan media sosial">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </template>
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
                            <label class="fw-bold small text-muted mb-2">Nomor WhatsApp Toko</label>
                            <input type="tel" inputmode="tel" name="store_phone" class="form-control bg-light" value="{{ $setting->store_phone }}" required>
                            <div class="form-text">Nomor Indonesia seperti 0812... otomatis disesuaikan. Untuk nomor negara lain, gunakan awalan +, misalnya +1...</div>
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
    .setting-logo-input {
        min-width: 0;
    }

    .landing-image-preview {
        display: flex;
        justify-content: center;
        max-width: 500px;
        padding: 0.5rem;
        background-color: var(--bg-cream);
    }

    .landing-image-preview img {
        display: block;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 300px;
        object-fit: contain;
    }

    .nav-pills .nav-link.active {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
</style>

<script>
    (() => {
        const imageInput = document.getElementById('landingImageInput');
        const resolutionStatus = document.getElementById('landingImageResolution');
        const currentImage = document.querySelector('[data-landing-image-preview]');
        const minimumLongEdge = 1400;

        if (!imageInput || !resolutionStatus) {
            return;
        }

        const showResolution = (width, height, label) => {
            const isLowResolution = Math.max(width, height) < minimumLongEdge;
            resolutionStatus.classList.toggle('text-warning', isLowResolution);
            resolutionStatus.classList.toggle('text-success', !isLowResolution);
            resolutionStatus.textContent = `${label}: ${width} × ${height} px. ${
                isLowResolution
                    ? `Sisi terpanjang kurang dari ${minimumLongEdge} px; gambar mungkin tampak pecah di landing page.`
                    : 'Dimensi sumber memadai; ketajaman juga dipengaruhi kualitas dan kompresi file.'
            }`;
        };

        if (currentImage) {
            const showCurrentResolution = () => {
                showResolution(currentImage.naturalWidth, currentImage.naturalHeight, 'Resolusi gambar saat ini');
            };

            if (currentImage.complete) {
                if (currentImage.naturalWidth > 0) {
                    showCurrentResolution();
                } else {
                    resolutionStatus.textContent = 'Resolusi gambar saat ini tidak dapat dibaca.';
                }
            } else {
                currentImage.addEventListener('load', showCurrentResolution, { once: true });
                currentImage.addEventListener('error', () => {
                    resolutionStatus.textContent = 'Resolusi gambar saat ini tidak dapat dibaca.';
                }, { once: true });
            }
        }

        imageInput.addEventListener('change', () => {
            const file = imageInput.files?.[0];

            if (!file) {
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            const image = new Image();
            image.addEventListener('load', () => {
                URL.revokeObjectURL(objectUrl);
                if (imageInput.files?.[0] === file) {
                    showResolution(image.naturalWidth, image.naturalHeight, 'Resolusi file terpilih');
                }
            }, { once: true });
            image.addEventListener('error', () => {
                URL.revokeObjectURL(objectUrl);
                if (imageInput.files?.[0] === file) {
                    resolutionStatus.textContent = 'Dimensi gambar tidak dapat dibaca; pilih file gambar yang valid.';
                }
            }, { once: true });
            image.src = objectUrl;
        });

        const list = document.getElementById('socialLinksList');
        const template = document.getElementById('socialLinkTemplate');
        const addButton = document.getElementById('addSocialLink');
        if (!list || !template || !addButton) return;

        let nextIndex = 0;
        list.querySelectorAll('input[name^="social_links["]').forEach((input) => {
            const match = input.name.match(/^social_links\[(\d+)\]/);
            if (match) nextIndex = Math.max(nextIndex, Number(match[1]) + 1);
        });
        const setAddButtonState = () => {
            addButton.disabled = list.querySelectorAll('[data-social-link-row]').length >= 10;
        };

        addButton.addEventListener('click', () => {
            if (list.querySelectorAll('[data-social-link-row]').length >= 10) return;

            list.querySelector('[data-social-links-empty]')?.remove();
            const row = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
            list.insertAdjacentHTML('beforeend', row);
            setAddButtonState();
        });

        list.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-social-link]');
            if (!removeButton) return;

            removeButton.closest('[data-social-link-row]')?.remove();
            if (list.querySelectorAll('[data-social-link-row]').length === 0) {
                const emptyState = document.createElement('p');
                emptyState.className = 'small text-muted mb-0';
                emptyState.dataset.socialLinksEmpty = '';
                emptyState.textContent = 'Belum ada tautan media sosial.';
                list.appendChild(emptyState);
            }
            setAddButtonState();
        });

        setAddButtonState();
    })();
</script>
@endsection
