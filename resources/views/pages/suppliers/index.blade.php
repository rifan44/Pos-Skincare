@extends('layouts.app')

@section('title', 'Supplier | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h3 class="font-serif fw-bold mb-1">Supplier</h3><p class="text-muted mb-0">Kelola sumber barang masuk dan kontak pemasok.</p></div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-dark rounded-pill"><i class="fas fa-plus me-2"></i>Tambah Supplier</a>
    </div>
    @include('partials.flash')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="px-4">Nama</th><th>Kontak</th><th>Status</th><th>Pembelian</th><th class="text-end px-4">Aksi</th></tr></thead>
            <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="px-4"><strong>{{ $supplier->name }}</strong><div class="small text-muted">{{ $supplier->phone ?: 'Tidak ada nomor' }}</div></td>
                    <td>{{ $supplier->contact_name ?: '-' }}</td>
                    <td><span class="badge rounded-pill {{ $supplier->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>{{ $supplier->purchases_count }}</td>
                    <td class="text-end px-4">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-light border rounded-pill"><i class="fas fa-pen"></i></a>
                        @if($supplier->purchases_count === 0)
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" data-confirm-delete data-confirm-title="Hapus supplier?" data-confirm-text="Data supplier akan dipindahkan ke trash.">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-light border rounded-pill text-danger"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">Belum ada supplier.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="p-3">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
