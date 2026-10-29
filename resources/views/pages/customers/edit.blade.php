@extends('layouts.app')
@section('title', 'Edit Customer | Skincare POS')
@section('content')
<div class="container py-4"><div class="row justify-content-center"><div class="col-lg-8">
<div class="d-flex justify-content-between align-items-center mb-4"><h3 class="font-serif fw-bold mb-0">Edit Customer</h3><a href="{{ route('customers.index') }}" class="btn btn-light border rounded-pill">Kembali</a></div>
<form action="{{ route('customers.update', $customer) }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">@csrf @method('PUT')
    @include('pages.customers.form')
    <button class="btn btn-dark rounded-pill px-4 mt-3">Simpan Perubahan</button>
</form></div></div></div>
@endsection
