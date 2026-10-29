@extends('layouts.app')

@section('title', 'Penyesuaian Stok | Skincare POS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-serif fw-bold mb-1">Penyesuaian Stok</h3>
                    <p class="text-muted mb-0">Semua koreksi stok wajib memiliki alasan dan masuk ke ledger.</p>
                </div>
                <a href="{{ route('stock-movements.index') }}" class="btn btn-light border rounded-pill">Kembali</a>
            </div>

            @include('partials.flash')

            <form action="{{ route('stock-adjustments.store') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Produk</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Pilih produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id', request('product_id')) == $product->id)>
                                    {{ $product->name }}{{ $product->sku ? ' · '.$product->sku : '' }} · stok {{ $product->stock }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis perubahan</label>
                        <select name="direction" class="form-select" required>
                            <option value="in" @selected(old('direction') === 'in')>Tambah stok</option>
                            <option value="out" @selected(old('direction') === 'out')>Kurangi stok</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jumlah unit</label>
                        <input type="number" name="quantity" min="1" class="form-control" value="{{ old('quantity') }}" required>
                        @error('quantity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alasan / catatan</label>
                        <textarea name="reason" class="form-control" rows="4" minlength="5" maxlength="1000" required placeholder="Contoh: Hasil stok opname 29 Oktober 2026; 2 unit rusak karena kemasan bocor.">{{ old('reason') }}</textarea>
                        @error('reason')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button class="btn btn-dark rounded-pill px-4 mt-4">Simpan Penyesuaian</button>
            </form>
        </div>
    </div>
</div>
@endsection
