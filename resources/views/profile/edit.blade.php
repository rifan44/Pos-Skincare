@extends('layouts.app')

@section('title', 'Profil Akun | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Profil Akun</h3>
            <p class="text-muted mb-0">Kelola informasi akun dan keamanan kata sandi Anda.</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm rounded-4" role="status">
            Perubahan berhasil disimpan.
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 mb-4">
        <div class="mb-4">
            <h5 class="font-serif fw-bold text-dark mb-1">Informasi Profil</h5>
            <p class="text-muted small mb-0">Perbarui nama dan alamat email akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
            @csrf
            @method('PATCH')

            <div class="col-12 col-lg-6">
                <label for="name" class="form-label fw-semibold">Nama</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-lg-6">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Profil</button>
            </div>
        </form>
    </section>

    <section class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 mb-4">
        <div class="mb-4">
            <h5 class="font-serif fw-bold text-dark mb-1">Ubah Kata Sandi</h5>
            <p class="text-muted small mb-0">Gunakan kata sandi yang kuat untuk menjaga keamanan akun.</p>
        </div>

        @if($errors->updatePassword->any())
            <div class="alert alert-danger border-0 rounded-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->updatePassword->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-12 col-lg-4">
                <label for="update_password_current_password" class="form-label fw-semibold">Kata Sandi Saat Ini</label>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
            </div>

            <div class="col-12 col-lg-4">
                <label for="update_password_password" class="form-label fw-semibold">Kata Sandi Baru</label>
                <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" required>
            </div>

            <div class="col-12 col-lg-4">
                <label for="update_password_password_confirmation" class="form-label fw-semibold">Konfirmasi Kata Sandi Baru</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Kata Sandi</button>
            </div>
        </form>
    </section>

    @if($user->role !== 'admin')
        <section class="card border border-danger-subtle shadow-sm rounded-4 p-4 p-lg-5">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div>
                    <h5 class="font-serif fw-bold text-dark mb-1">Hapus Akun</h5>
                    <p class="text-muted small mb-0">Penghapusan akun tidak dapat dibatalkan.</p>
                </div>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#confirmAccountDeletion">
                    Hapus Akun
                </button>
            </div>
        </section>

        <div class="modal fade" id="confirmAccountDeletion" tabindex="-1" aria-labelledby="confirmAccountDeletionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="modal-header">
                            <h5 class="modal-title font-serif fw-bold" id="confirmAccountDeletionLabel">Konfirmasi Hapus Akun</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Masukkan kata sandi untuk mengonfirmasi penghapusan akun secara permanen.</p>
                            <label for="delete_account_password" class="form-label fw-semibold">Kata Sandi</label>
                            <input id="delete_account_password" name="password" type="password" class="form-control" autocomplete="current-password" required>
                            @foreach($errors->userDeletion->get('password') as $error)
                                <div class="form-text text-danger">{{ $error }}</div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4">Hapus Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
