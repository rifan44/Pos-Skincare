@extends('layouts.app')
@section('title', 'Catat Utang Piutang | Skincare POS')
@section('content')
<div class="container py-4"><div class="row justify-content-center"><div class="col-lg-8">
<div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="font-serif fw-bold mb-1">Catat Utang / Piutang</h3><p class="text-muted mb-0">Simpan kewajiban atau tagihan yang belum lunas.</p></div><a href="{{ route('debts.index') }}" class="btn btn-light border rounded-pill">Kembali</a></div>
<form action="{{ route('debts.store') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">@csrf
    @include('pages.debts.form')
    <button class="btn btn-dark rounded-pill px-4 mt-3">Simpan Catatan</button>
</form></div></div></div>
@endsection
