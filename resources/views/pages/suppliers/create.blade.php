@extends('layouts.app')
@section('title', 'Tambah Supplier | Skincare POS')
@section('content')
<div class="container py-4"><div class="row justify-content-center"><div class="col-lg-8">
<div class="d-flex justify-content-between align-items-center mb-4"><h3 class="font-serif fw-bold mb-0">Tambah Supplier</h3><a href="{{ route('suppliers.index') }}" class="btn btn-light border rounded-pill">Kembali</a></div>
<form action="{{ route('suppliers.store') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">@csrf
    @include('pages.suppliers.form', ['supplier' => null])
    <button class="btn btn-dark rounded-pill px-4">Simpan Supplier</button>
</form></div></div></div>
@endsection
