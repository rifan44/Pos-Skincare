@extends('layouts.app')

@section('title', 'Edit Kategori | POS Skincare')

@section('content')
<div class="container py-5">
    <div class="row mt-4">
        <div class="col-md-6 mx-auto">
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Edit Kategori</h5>
                </div>
                <div class="card-body">
                    <!-- Form diarahkan ke update, butuh ID kategori -->
                    <form action="{{ route('categories.update', $category->id) }}" method="POST">
                        @csrf 
                        <!-- Di Laravel, form update wajib memakai method PUT -->
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <!-- Value diisi dengan nama kategori yang lama -->
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_type" class="form-label">Kelompok Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_type') is-invalid @enderror" id="category_type" name="category_type" required>
                                <option value="jenis" @selected(old('category_type', $category->category_type) === 'jenis')>Jenis skincare</option>
                                <option value="merek" @selected(old('category_type', $category->category_type) === 'merek')>Merek skincare</option>
                            </select>
                            @error('category_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kategori yang sudah dipakai produk tidak dapat dipindahkan kelompok sebelum produk diperbarui.</div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('categories.index') }}" class="btn btn-light rounded-pill me-2">Batal</a>
                            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
