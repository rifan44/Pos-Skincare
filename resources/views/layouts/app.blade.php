@php
    $appSetting = \App\Models\Setting::first();
    // Menggunakan warna dusty rose/terracotta dari referensi jika belum disetting
    $primaryColor = $appSetting->primary_color ?? '#c78b7d'; 
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $appSetting->store_name ?? 'Skincare POS')</title>
    @include('partials.theme-script')
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Playfair Display (Serif) & Plus Jakarta Sans (Modern) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Palet Warna Premium berdasarkan referensi */
            --bg-cream: #fdfbf7;
            --card-bg: #ffffff;
            --text-dark: #3b3331;
            --text-muted: #8a7b78;
            --primary-color: {{ $primaryColor }};
            --primary-hover: #b07466;
            --border-color: #f0e9e4;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-cream); 
            color: var(--text-dark); 
        }

        /* Tipografi Elegan untuk Judul */
        h1, h2, h3, h4, .font-serif {
            font-family: 'Playfair Display', serif;
            color: var(--text-dark);
        }

        /* Standarisasi Kartu */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color) !important;
            border-radius: 1.25rem !important;
            box-shadow: 0 10px 30px rgba(59, 51, 49, 0.04) !important;
        }

        /* Standarisasi Tombol Utama */
        .btn-dark {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-dark:hover {
            background-color: var(--primary-hover) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(199, 139, 125, 0.3);
        }
        
        .btn-outline-dark {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        .btn-outline-dark:hover {
            background-color: var(--primary-color) !important;
            color: #fff !important;
        }
        
        /* Input & Form */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            background-color: #fff !important;
            border-radius: 0.75rem;
            padding: 0.6rem 1rem;
            color: var(--text-dark);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(199, 139, 125, 0.15);
        }
        
        /* Tabel */
        .table-light th {
            background-color: #f7f3ef !important;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: none;
        }
        td { color: var(--text-dark); border-color: var(--border-color); }
    </style>
    @include('partials.theme-styles')
    <style>
        .table-responsive > .table.table-scrollable {
            min-width: 56rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')
    <main>
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const offcanvas = document.getElementById('offcanvasNavbar');

            if (offcanvas && window.bootstrap?.Offcanvas) {
                offcanvas.querySelectorAll('[data-nav-link]').forEach((link) => {
                    link.addEventListener('click', (event) => {
                        const instance = window.bootstrap.Offcanvas.getInstance(offcanvas);

                        if (!instance || !offcanvas.classList.contains('show')) {
                            return;
                        }

                        const target = new URL(link.href, window.location.href);
                        const current = new URL(window.location.href);
                        const samePage = target.origin === current.origin
                            && target.pathname === current.pathname
                            && target.search === current.search;

                        if (!samePage) {
                            instance.hide();
                            return;
                        }

                        event.preventDefault();

                        offcanvas.addEventListener('hidden.bs.offcanvas', () => {
                            const nextLocation = `${target.pathname}${target.search}${target.hash}`;
                            const currentLocation = `${current.pathname}${current.search}${current.hash}`;

                            if (nextLocation !== currentLocation) {
                                window.history.pushState(null, '', nextLocation);
                            }

                            if (target.hash) {
                                document.getElementById(decodeURIComponent(target.hash.slice(1)))
                                    ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            } else {
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        }, { once: true });

                        instance.hide();
                    });
                });
            }

            document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    Swal.fire({
                        title: form.dataset.confirmTitle || 'Hapus data?',
                        text: form.dataset.confirmText || 'Data yang dihapus tidak lagi tampil di daftar aktif.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger rounded-pill px-4 ms-2',
                            cancelButton: 'btn btn-light border rounded-pill px-4',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
