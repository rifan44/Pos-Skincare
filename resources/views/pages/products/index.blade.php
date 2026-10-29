@extends('layouts.app')

@section('title', 'Data Produk | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Data Produk Skincare</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola produk, jenis skincare, merek, harga jual, dan stok tersedia.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('products.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 p-3 p-lg-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-3">
                <label for="search" class="form-label small fw-bold text-muted">Cari Produk</label>
                <input type="search" id="search" class="form-control bg-light" value="{{ $filters['search'] ?? '' }}" placeholder="Nama, SKU, jenis, atau merek" autocomplete="off">
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="category_filter" class="form-label small fw-bold text-muted">Jenis Skincare</label>
                <input type="search" id="category_filter" class="form-control bg-light" value="{{ $filters['category'] ?? '' }}" placeholder="Ketik nama jenis" autocomplete="off">
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="brand_filter" class="form-label small fw-bold text-muted">Merek</label>
                <input type="search" id="brand_filter" class="form-control bg-light" value="{{ $filters['brand'] ?? '' }}" placeholder="Ketik nama merek" autocomplete="off">
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="sort" class="form-label small fw-bold text-muted">Urutkan nama</label>
                <select id="sort" class="form-select bg-light">
                    <option value="az" @selected(($filters['sort'] ?? 'az') === 'az')>A–Z</option>
                    <option value="za" @selected(($filters['sort'] ?? 'az') === 'za')>Z–A</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3 d-flex gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-light border rounded-pill px-4">Reset</a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-scrollable">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="px-4 py-3 text-center">No</th>
                        <th width="8%" class="px-4 py-3 text-center">Foto</th>
                        <th width="14%" class="px-4 py-3 text-start">Jenis</th>
                        <th width="14%" class="px-4 py-3 text-start">Merek</th>
                        <th class="px-4 py-3 text-start">Nama Produk</th>
                        <th width="12%" class="px-4 py-3 text-start">HPP</th>
                        <th width="12%" class="px-4 py-3 text-start">Harga Jual</th>
                        <th width="8%" class="px-4 py-3 text-center">Stok</th>
                        <th width="15%" class="px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="products_table_body">
                    @foreach($products as $index => $product)
                    <tr data-product-row data-name="{{ $product->name }}" data-search="{{ $product->name }} {{ $product->sku }} {{ $product->category->name ?? '' }} {{ $product->brand->name ?? '' }}" data-type="{{ $product->category->name ?? '' }}" data-brand="{{ $product->brand->name ?? '' }}">
                        <td class="px-4 py-3 text-center text-muted" data-row-number>{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="rounded-3 border" style="width: 45px; height: 45px; object-fit: cover;">
                            @else
                                <div class="rounded-3 border d-inline-flex align-items-center justify-content-center bg-light text-muted" style="width: 45px; height: 45px;">
                                    <i class="fas fa-image" style="font-size: 1rem;"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-start"><span class="badge bg-light text-dark border px-2 py-1">{{ $product->category->name ?? '-' }}</span></td>
                        <td class="px-4 py-3 text-start"><span class="badge text-white px-2 py-1" style="background-color: var(--primary-color);">{{ $product->brand->name ?? '-' }}</span></td>
                        <td class="px-4 py-3 fw-bold text-dark">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-muted text-start">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 fw-bold text-success text-start">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center"><span class="badge {{ $product->stock < 10 ? 'bg-danger' : 'bg-success' }} rounded-pill px-2">{{ $product->stock }}</span></td>
                        <td class="px-4 py-3">
                            <!-- PERBAIKAN TOMBOL AKSI -->
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">
                                    Edit
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="m-0" data-confirm-delete data-confirm-title="Hapus produk?" data-confirm-text="Produk {{ $product->name }} akan dihapus dari daftar aktif.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr id="empty-products-row" class="{{ $products->isEmpty() ? '' : 'd-none' }}">
                        <td colspan="9" class="text-center text-muted py-5 fw-medium">
                            {{ filled($filters['search'] ?? null) || filled($filters['category'] ?? null) || filled($filters['brand'] ?? null) ? 'Tidak ada produk yang cocok dengan pencarian.' : 'Belum ada produk terdaftar.' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    (() => {
        const searchInput = document.getElementById('search');
        const typeInput = document.getElementById('category_filter');
        const brandInput = document.getElementById('brand_filter');
        const sortInput = document.getElementById('sort');
        const tableBody = document.getElementById('products_table_body');
        const emptyRow = document.getElementById('empty-products-row');
        const rows = [...tableBody.querySelectorAll('[data-product-row]')];
        const collator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });

        const normalize = value => String(value || '').trim().toLocaleLowerCase('id');

        function filterProducts() {
            const query = normalize(searchInput.value);
            const typeQuery = normalize(typeInput.value);
            const brandQuery = normalize(brandInput.value);
            const direction = sortInput.value === 'za' ? -1 : 1;
            const visibleRows = rows.filter(row => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesType = !typeQuery || normalize(row.dataset.type).includes(typeQuery);
                const matchesBrand = !brandQuery || normalize(row.dataset.brand).includes(brandQuery);

                return matchesSearch && matchesType && matchesBrand;
            }).sort((first, second) => direction * collator.compare(first.dataset.name, second.dataset.name));
            const visibleSet = new Set(visibleRows);

            visibleRows.forEach((row, index) => {
                row.querySelector('[data-row-number]').textContent = String(index + 1);
                row.classList.remove('d-none');
                tableBody.appendChild(row);
            });
            rows.filter(row => !visibleSet.has(row)).forEach(row => {
                row.classList.add('d-none');
                tableBody.appendChild(row);
            });
            emptyRow.classList.toggle('d-none', visibleRows.length > 0);
            emptyRow.querySelector('td').textContent = rows.length > 0
                ? 'Tidak ada produk yang cocok dengan pencarian.'
                : 'Belum ada produk terdaftar.';
        }

        [searchInput, typeInput, brandInput].forEach(input => input.addEventListener('input', filterProducts));
        sortInput.addEventListener('change', filterProducts);
        filterProducts();
    })();
</script>
@endsection
