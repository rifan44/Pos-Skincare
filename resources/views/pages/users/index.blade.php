@extends('layouts.app')

@section('title', 'Kelola Pegawai | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <!-- HEADER HALAMAN -->
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Kelola Pegawai</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Manajemen akses login admin dan kasir toko.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <button class="btn btn-dark rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Pegawai
            </button>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger fw-medium rounded-3 shadow-sm border-0 mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- KARTU TABEL -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="25%" class="px-4 py-3 text-start">Nama Lengkap</th>
                        <th class="px-4 py-3 text-start">Email Login</th>
                        <th width="15%" class="px-4 py-3 text-center">Hak Akses</th>
                        <th width="20%" class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-dark text-start">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-muted text-start">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($user->role === 'admin')
                                <span class="badge rounded-pill px-3 py-2 bg-dark text-white shadow-sm">Admin</span>
                            @else
                                <span class="badge rounded-pill px-3 py-2 bg-light border text-dark shadow-sm">Kasir</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <button class="btn btn-sm btn-light border rounded-pill px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}">
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium">Hapus</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>Akun aktif</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5 fw-medium">
                            <i class="fas fa-users fs-2 mb-3 opacity-50 d-block"></i>
                            Belum ada akun pegawai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($users as $user)
    <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title font-serif fw-bold" id="modalEditLabel{{ $user->id }}">Edit Akun Pegawai</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3 text-start">
                            <label class="fw-bold small mb-1 text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="fw-bold small mb-1 text-muted">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="fw-bold small mb-1 text-muted">Hak Akses</label>
                            @if($user->id === auth()->id())
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <input type="text" class="form-control" value="{{ ucfirst($user->role) }} (akun aktif)" disabled>
                            @else
                                <select name="role" class="form-select" required>
                                    <option value="kasir" {{ $user->role === 'kasir' ? 'selected' : '' }}>Kasir (POS Saja)</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                                </select>
                            @endif
                        </div>
                        <div class="mb-1 text-start">
                            <label class="fw-bold small mb-1 text-muted">Reset Password <span class="fw-normal text-danger">(Kosongkan jika tidak diganti)</span></label>
                            <input type="password" name="password" class="form-control" minlength="8" placeholder="Minimal 8 karakter">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Tambah Pegawai -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-serif fw-bold">Tambah Akun Pegawai</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold small mb-1 text-muted">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small mb-1 text-muted">Email (Untuk Login)</label>
                        <input type="email" name="email" class="form-control" required placeholder="budi@toko.com">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small mb-1 text-muted">Hak Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="kasir">Kasir (Hanya akses POS & Struk)</option>
                            <option value="admin">Admin (Akses penuh seluruh sistem)</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="fw-bold small mb-1 text-muted">Password Awal</label>
                        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">Buat Akun Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
