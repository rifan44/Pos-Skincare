@php
    $appSetting = \App\Models\Setting::first();
    $primaryColor = $appSetting->primary_color ?? '#c78b7d';
@endphp

<nav class="navbar navbar-expand-lg sticky-top py-3" style="background-color: rgba(253, 251, 247, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #f0e9e4;">
    <div class="container-fluid px-lg-5">
        
        <!-- Logo Kiri -->
        <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="{{ url('/') }}">
            @if($appSetting && $appSetting->logo)
                <img src="{{ asset('storage/' . $appSetting->logo) }}" class="rounded-circle shadow-sm" width="38" height="38" style="object-fit: cover;">
            @else
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; background-color: {{ $primaryColor }}; font-size: 14px;">
                    {{ substr($appSetting->store_name ?? 'SK', 0, 2) }}
                </div>
            @endif
            <span class="font-serif fw-bold fs-4" style="color: var(--text-dark); letter-spacing: 0.5px;">
                {{ $appSetting->store_name ?? 'Skincare POS' }}
            </span>
        </a>

        <!-- Tombol Hamburger (Mobile) -->
        <button class="navbar-toggler border-0 shadow-none px-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
            <i class="fas fa-bars fs-4 text-dark"></i>
        </button>

        <div class="offcanvas offcanvas-end" style="background-color: var(--bg-cream);" tabindex="-1" id="offcanvasNavbar">
            <div class="offcanvas-header border-bottom py-3">
                <h6 class="offcanvas-title font-serif fw-bold mb-0">Navigasi</h6>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                
                <!-- Menu Tengah -->
                <ul class="navbar-nav justify-content-center align-items-center flex-grow-1 gap-2">
                    @auth
                        <!-- Menu Khusus Pengguna Login -->
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 rounded-pill {{ request()->routeIs('dashboard.*') ? 'fw-bold' : '' }}" style="{{ request()->routeIs('dashboard.*') ? 'background-color: #f7f3ef; color: var(--primary-color);' : 'color: var(--text-muted);' }}" href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 rounded-pill {{ request()->routeIs('categories.*') ? 'fw-bold' : '' }}" style="{{ request()->routeIs('categories.*') ? 'background-color: #f7f3ef; color: var(--primary-color);' : 'color: var(--text-muted);' }}" href="{{ route('categories.index') }}">Kategori</a></li>
                            <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 rounded-pill {{ request()->routeIs('products.*') ? 'fw-bold' : '' }}" style="{{ request()->routeIs('products.*') ? 'background-color: #f7f3ef; color: var(--primary-color);' : 'color: var(--text-muted);' }}" href="{{ route('products.index') }}">Produk</a></li>
                            <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 rounded-pill {{ request()->routeIs('reports.*') ? 'fw-bold' : '' }}" style="{{ request()->routeIs('reports.*') ? 'background-color: #f7f3ef; color: var(--primary-color);' : 'color: var(--text-muted);' }}" href="{{ route('reports.index') }}">Laporan</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 rounded-pill {{ request()->routeIs('history.*') ? 'fw-bold' : '' }}" style="{{ request()->routeIs('history.*') ? 'background-color: #f7f3ef; color: var(--primary-color);' : 'color: var(--text-muted);' }}" href="{{ route('history.index') }}">Riwayat</a></li>
                    @else
                        <!-- Menu Khusus Tamu (Guest) -->
                        <li class="nav-item"><a class="nav-link fw-bold px-3 py-2 hover-primary" href="{{ url('/') }}" style="color: var(--text-dark);">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 hover-primary" href="#features" style="color: var(--text-muted);">Fitur Utama</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 hover-primary" href="#" style="color: var(--text-muted);">Tentang Kami</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium px-3 py-2 hover-primary" href="#" style="color: var(--text-muted);">Kontak</a></li>
                    @endauth
                </ul>
                
                <!-- Area Kanan (Tombol & Profil) -->
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 mt-3 mt-lg-0">
                    @auth
                        <!-- Area Kanan Khusus Pengguna Login -->
                        <a href="{{ route('pos.index') }}" class="btn fw-bold px-4 py-2 rounded-pill shadow-sm" style="background-color: var(--primary-color); color: #fff; font-size: 0.9rem;">
                            Buka Kasir <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        
                        <div class="dropdown">
                            <button class="btn dropdown-toggle fw-medium border rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2" style="background-color: #fff; color: var(--text-dark); font-size: 0.9rem;" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fs-6" style="color: var(--primary-color);"></i> 
                                <span>{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 p-2 rounded-4" style="background-color: var(--card-bg);">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item rounded-3 py-2 fw-medium text-dark" href="{{ route('users.index') }}">Pegawai</a></li>
                                    <li><a class="dropdown-item rounded-3 py-2 fw-medium text-dark" href="{{ route('settings.index') }}">Pengaturan</a></li>
                                    <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 fw-bold" style="color: var(--primary-color);">Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Area Kanan Khusus Tamu (Guest) -->
                        <a href="{{ route('login') }}" class="btn fw-bold px-4 py-2 rounded-pill" style="border: 1px solid var(--primary-color); color: var(--primary-color); background-color: transparent;">
                            Masuk Sistem
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Tambahan efek hover untuk menu tamu -->
<style>
    .hover-primary { transition: color 0.3s ease; }
    .hover-primary:hover { color: var(--primary-color) !important; }
</style>