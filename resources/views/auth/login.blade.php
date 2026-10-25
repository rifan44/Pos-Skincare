<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Skincare POS</title>
    
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            padding: 2.5rem;
            width: 90%;
            max-width: 400px;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mt-1 { margin-top: 0.25rem; }
        .d-block { display: block; }
        .w-100 { width: 100%; box-sizing: border-box; }
        .text-muted { color: #6c757d; font-size: 0.9rem; }
        .text-danger { color: #dc3545; font-size: 0.8rem; font-weight: 600; margin-top: 5px; display: block; }
        .fw-bold { font-weight: 700; }
        .fw-extrabold { font-weight: 800; }
        
        .custom-input {
            border-radius: 0.8rem;
            border: 1.5px solid #eaeaea;
            padding: 0.8rem 1rem;
            background: #fafafa;
            transition: all 0.3s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .custom-input:focus {
            border-color: #ffb6b9;
            box-shadow: 0 0 0 4px rgba(255,182,185,0.15);
            outline: none;
            background: #fff;
        }
        
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .eye-icon {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #9ca3af;
        }
        
        .btn-login {
            background-color: #2b2d42;
            color: #ffffff;
            border-radius: 0.8rem;
            padding: 0.9rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
            margin-top: 10px;
        }
        .btn-login:hover {
            background-color: #1a1b28;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 27, 40, 0.2);
        }
    </style>
    @include('partials.theme-script')
    @include('partials.theme-styles')
</head>
<body>

    <button class="theme-toggle guest-theme-toggle" type="button" data-theme-toggle aria-label="Gunakan tema gelap" title="Gunakan tema gelap">
        <span data-theme-icon aria-hidden="true">☾</span>
    </button>

    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-extrabold" style="margin:0; font-size: 1.8rem; color: #2b2d42; letter-spacing: -0.5px;">
                Skincare<span style="color: #ffb6b9;">.POS</span>
            </h2>
            <p class="text-muted mt-1" style="margin-bottom:0;">Silakan masuk ke akun Anda</p>
        </div>

        @if (session('status'))
            <div style="color: #198754; font-size: 0.9rem; margin-bottom: 1rem; text-align: center; font-weight: 600;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="fw-bold d-block mb-1" style="font-size: 0.9rem; color: #495057;">Email</label>
                <input id="email" class="custom-input w-100" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@toko.com">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="fw-bold d-block mb-1" style="font-size: 0.9rem; color: #495057;">Password</label>
                <div class="password-wrapper">
                    <input id="password" class="custom-input w-100" type="password" name="password" required placeholder="••••••••">
                    <i class="fas fa-eye eye-icon" id="togglePassword"></i>
                </div>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-3" style="display: flex; align-items: center; gap: 8px;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #2b2d42;">
                <label for="remember_me" class="text-muted" style="cursor: pointer; font-size: 0.9rem; font-weight: 500;">Ingat Saya</label>
            </div>

            <!-- Tombol Login -->
            <button type="submit" class="btn-login w-100">
                Masuk Sistem
            </button>
        </form>
    </div>

    <!-- Script untuk Toggle Mata Password -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>