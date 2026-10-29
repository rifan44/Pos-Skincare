@extends('layouts.app')
@section('title', 'Customer | Skincare POS')
@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h3 class="font-serif fw-bold mb-1">Customer & Reseller</h3><p class="text-muted mb-0">Simpan identitas customer agar transaksi lebih konsisten.</p></div>
        <a href="{{ route('customers.create') }}" class="btn btn-dark rounded-pill"><i class="fas fa-plus me-2"></i>Tambah Customer</a>
    </div>
    @include('partials.flash')
    <form class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="input-group"><input name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama atau nomor telepon"><button class="btn btn-outline-dark">Cari</button></div>
    </form>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="px-4">Customer</th><th>Tipe</th><th>Status</th><th>Transaksi</th><th class="text-end px-4">Aksi</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td class="px-4"><strong>{{ $customer->name }}</strong><div class="small text-muted">{{ $customer->phone ?: 'Tidak ada nomor' }}</div></td>
                    <td>{{ ucfirst($customer->customer_type) }}</td>
                    <td><span class="badge rounded-pill {{ $customer->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>{{ $customer->transactions_count }}</td>
                    <td class="text-end px-4">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-light border rounded-pill"><i class="fas fa-pen"></i></a>
                        @if($customer->transactions_count === 0)
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" data-confirm-delete data-confirm-title="Hapus customer?" data-confirm-text="Data customer akan dipindahkan ke trash.">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-light border rounded-pill text-danger"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">Belum ada customer.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="p-3">{{ $customers->links() }}</div>
    </div>
</div>
@endsection
