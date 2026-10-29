@extends('layouts.app')

@section('title', 'Kelola Kategori | Skincare POS')

@section('content')
@php
    $categoryGroups = [
        [
            'key' => 'jenis',
            'title' => 'Jenis Skincare',
            'description' => 'Serum, sunscreen, toner, dan jenis lainnya.',
            'categories' => $categories->where('category_type', 'jenis')->values(),
        ],
        [
            'key' => 'merek',
            'title' => 'Merek Skincare',
            'description' => 'Kelola nama merek yang tersedia di toko.',
            'categories' => $categories->where('category_type', 'merek')->values(),
        ],
    ];
@endphp

<div class="container-fluid py-4 px-lg-5">
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Kategori Produk</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola kategori berdasarkan jenis skincare dan mereknya.</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('categories.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Tambah Kategori
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show fw-medium rounded-3 shadow-sm border-0 mb-4" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        @foreach($categoryGroups as $group)
        <div class="col-12 col-lg-6">
            <section class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" data-category-group="{{ $group['key'] }}">
                <div class="p-4 border-bottom" style="border-color: var(--border-color) !important;">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="font-serif fw-bold text-dark mb-1">{{ $group['title'] }}</h5>
                            <p class="small text-muted mb-0">{{ $group['description'] }}</p>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 {{ $group['key'] === 'jenis' ? 'bg-light text-dark border' : 'text-white' }}" @if($group['key'] === 'merek') style="background-color: var(--primary-color);" @endif>
                            {{ $group['categories']->count() }}
                        </span>
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-7">
                            <label class="visually-hidden" for="search_{{ $group['key'] }}">Cari {{ strtolower($group['title']) }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3"><i class="fas fa-search text-muted"></i></span>
                                <input type="search" id="search_{{ $group['key'] }}" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Cari {{ strtolower($group['title']) }}..." data-category-search>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <label class="visually-hidden" for="sort_{{ $group['key'] }}">Urutkan {{ strtolower($group['title']) }}</label>
                            <select id="sort_{{ $group['key'] }}" class="form-select bg-light rounded-pill" data-category-sort>
                                <option value="az">Nama: A–Z</option>
                                <option value="za">Nama: Z–A</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">Nama kategori</th>
                                <th class="py-3 text-center">Produk</th>
                                <th class="px-3 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody data-category-rows>
                            @foreach($group['categories'] as $category)
                            <tr data-category-name="{{ $category->name }}">
                                <td class="px-3 py-3 fw-bold text-dark">{{ $category->name }}</td>
                                <td class="py-3 text-center text-muted">{{ $group['key'] === 'jenis' ? $category->products_count : $category->brand_products_count }}</td>
                                <td class="px-3 py-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">Edit</a>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="m-0" data-confirm-delete data-confirm-title="Hapus kategori?" data-confirm-text="Kategori {{ $category->name }} akan dihapus dari daftar aktif.">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="fas fa-trash-alt me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            <tr data-category-empty>
                                <td colspan="3" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fs-3 mb-2 opacity-50 d-block"></i>
                                    <span data-empty-message>{{ $group['categories']->isEmpty() ? 'Belum ada kategori.' : 'Kategori tidak ditemukan.' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.querySelectorAll('[data-category-group]').forEach(group => {
        const searchInput = group.querySelector('[data-category-search]');
        const sortSelect = group.querySelector('[data-category-sort]');
        const rowsContainer = group.querySelector('[data-category-rows]');
        const emptyRow = group.querySelector('[data-category-empty]');
        const emptyMessage = group.querySelector('[data-empty-message]');
        const rows = Array.from(rowsContainer.querySelectorAll('tr[data-category-name]'));
        const collator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });

        function updateCategories() {
            const query = searchInput.value.trim().toLocaleLowerCase('id');
            const direction = sortSelect.value === 'za' ? -1 : 1;
            const matchingRows = rows
                .filter(row => row.dataset.categoryName.toLocaleLowerCase('id').includes(query))
                .sort((a, b) => direction * collator.compare(a.dataset.categoryName, b.dataset.categoryName));
            const matchingSet = new Set(matchingRows);

            matchingRows.forEach(row => rowsContainer.insertBefore(row, emptyRow));
            rows.forEach(row => row.classList.toggle('d-none', !matchingSet.has(row)));
            emptyRow.classList.toggle('d-none', matchingRows.length > 0);
            emptyMessage.textContent = rows.length === 0 ? 'Belum ada kategori.' : 'Kategori tidak ditemukan.';
        }

        searchInput.addEventListener('input', updateCategories);
        sortSelect.addEventListener('change', updateCategories);
        updateCategories();
    });
</script>
@endsection
