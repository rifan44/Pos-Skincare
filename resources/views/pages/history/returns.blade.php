@extends('layouts.app')

@section('title', 'Retur dan Refund | Skincare POS')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row align-items-end mb-4">
        <div class="col-sm-8">
            <h3 class="font-serif fw-bold text-dark mb-1">Retur dan Refund</h3>
            <p class="text-muted mb-0">Transaksi #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }} · {{ $transaction->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
            <a href="{{ route('history.index') }}" class="btn btn-light border rounded-pill px-4">Kembali ke Riwayat</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <span class="small text-muted text-uppercase fw-bold">Nilai Transaksi</span>
                <h4 class="font-serif fw-bold mt-2 mb-0">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <span class="small text-muted text-uppercase fw-bold">Total Refund</span>
                <h4 class="font-serif fw-bold mt-2 mb-0">Rp {{ number_format($transaction->refunded_amount, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <span class="small text-muted text-uppercase fw-bold">Status</span>
                <h4 class="font-serif fw-bold mt-2 mb-0">
                    @if($transaction->return_status === 'refunded') Refund penuh
                    @elseif($transaction->return_status === 'partially_refunded') Refund sebagian
                    @else Selesai
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="p-4 border-bottom">
            <h5 class="font-serif fw-bold mb-1">Riwayat Retur</h5>
            <p class="small text-muted mb-0">Catatan retur tersimpan sebagai audit dan tidak mengubah struk penjualan asli.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Diproses oleh</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3 text-end">Refund</th>
                        <th class="px-4 py-3 text-end">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaction->returns->sortByDesc('created_at') as $transactionReturn)
                    <tr>
                        <td class="px-4 py-3">{{ $transactionReturn->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3">{{ $transactionReturn->reason }}</td>
                        <td class="px-4 py-3">{{ $transactionReturn->processed_by_name }}</td>
                        <td class="px-4 py-3">{{ $transactionReturn->refund_method === 'transfer' ? 'Transfer' : 'Tunai' }}</td>
                        <td class="px-4 py-3 text-end fw-bold">Rp {{ number_format($transactionReturn->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('history.returns.print', $transactionReturn) }}" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3">Cetak</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada retur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="font-serif fw-bold mb-1">Proses Retur</h5>
        @if($returnWindowOpen && $transaction->return_status !== 'refunded')
            <p class="text-muted small mb-4">Retur dapat diproses sampai {{ $returnWindowEndsAt->format('d M Y, H:i') }}. Periksa kondisi barang. Hanya jumlah yang layak dijual kembali yang dimasukkan ke stok. Dana refund diserahkan manual dan dicatat di sini.</p>
            <form action="{{ route('history.returns.store', $transaction) }}" method="POST">
                @csrf
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-center">Sudah Diretur</th>
                                <th class="text-center">Sisa Retur</th>
                                <th style="min-width: 130px">Jumlah Retur</th>
                                <th style="min-width: 160px">Layak Masuk Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->details as $detail)
                                @php($alreadyReturned = $returnedQuantities[$detail->id] ?? 0)
                                @php($remaining = max(0, $detail->quantity - $alreadyReturned))
                                <tr>
                                    <td>{{ $detail->product->name ?? 'Produk tidak tersedia' }}<br><small class="text-muted">Rp {{ number_format($detail->price, 0, ',', '.') }} / unit</small></td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-center">{{ $alreadyReturned }}</td>
                                    <td class="text-center fw-bold">{{ $remaining }}</td>
                                    <td><input type="number" class="form-control" name="items[{{ $detail->id }}][quantity]" min="0" max="{{ $remaining }}" value="{{ old('items.'.$detail->id.'.quantity', 0) }}" {{ $remaining === 0 ? 'disabled' : '' }}></td>
                                    <td><input type="number" class="form-control" name="items[{{ $detail->id }}][restocked_quantity]" min="0" max="{{ $remaining }}" value="{{ old('items.'.$detail->id.'.restocked_quantity', 0) }}" {{ $remaining === 0 ? 'disabled' : '' }}><small class="text-muted">Tidak melebihi jumlah retur</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="reason" class="form-label fw-bold">Alasan retur</label>
                        <textarea id="reason" name="reason" class="form-control" rows="3" minlength="5" maxlength="1000" required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label for="refund_method" class="form-label fw-bold">Metode refund</label>
                        <select id="refund_method" name="refund_method" class="form-select" required>
                            <option value="cash" @selected(old('refund_method', 'cash') === 'cash')>Tunai</option>
                            <option value="transfer" @selected(old('refund_method') === 'transfer')>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="reference" class="form-label fw-bold">Referensi transfer <small class="text-muted">(wajib untuk transfer)</small></label>
                        <input id="reference" type="text" name="reference" class="form-control" maxlength="255" value="{{ old('reference') }}" placeholder="Opsional">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan dan Catat Refund</button>
                </div>
            </form>
        @elseif(! $returnWindowOpen)
            <p class="text-muted mb-0">Masa retur 7 hari telah berakhir pada {{ $returnWindowEndsAt->format('d M Y, H:i') }}.</p>
        @else
            <p class="text-muted mb-0">Seluruh nilai transaksi sudah direfund.</p>
        @endif
    </div>
</div>
<script>
    const refundMethod = document.getElementById('refund_method');
    const refundReference = document.getElementById('reference');
    const updateRefundReferenceRequirement = () => {
        refundReference.required = refundMethod.value === 'transfer';
    };
    refundMethod.addEventListener('change', updateRefundReferenceRequirement);
    updateRefundReferenceRequirement();
</script>
@endsection
