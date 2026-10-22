<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Refund #{{ $transactionReturn->id }}</title>
    <style>
        body { margin: 0; padding: 16px; font-family: 'Courier New', monospace; font-size: 12px; color: #000; }
        .ticket { box-sizing: border-box; width: 100%; max-width: 80mm; margin: auto; }
        .center { text-align: center; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        td, th { padding: 3px 0; vertical-align: top; }
        .line { border-top: 1px dashed #000; }
        @media print { @page { margin: 0; } body { padding: 3mm; } }
    </style>
</head>
<body onload="window.print()">
<div class="ticket">
    <div class="center">
        <strong>{{ $setting?->store_name ?? 'Skincare POS' }}</strong>
        @if($setting?->store_address)<div>{{ $setting->store_address }}</div>@endif
        <div>Bukti Retur dan Refund</div>
    </div>
    <table>
        <tr><td>No. Refund</td><td class="right">#{{ str_pad($transactionReturn->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td>No. Transaksi</td><td class="right">#{{ str_pad($transactionReturn->transaction_id, 5, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td>Waktu</td><td class="right">{{ $transactionReturn->created_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Diproses oleh</td><td class="right">{{ $transactionReturn->processed_by_name }}</td></tr>
    </table>
    <table>
        <thead><tr class="line"><th>Barang</th><th class="right">Qty</th><th class="right">Refund</th></tr></thead>
        <tbody>
            @foreach($transactionReturn->details as $detail)
            <tr>
                <td>{{ $detail->transactionDetail->product->name ?? 'Produk tidak tersedia' }}<br><small>Nilai bersih baris struk: Rp {{ number_format($detail->transactionDetail->subtotal, 0, ',', '.') }} · stok kembali: {{ $detail->restocked_quantity }}</small></td>
                <td class="right">{{ $detail->quantity }}</td>
                <td class="right">{{ number_format($detail->refund_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr class="line"><th colspan="2">TOTAL REFUND</th><th class="right">Rp {{ number_format($transactionReturn->total_amount, 0, ',', '.') }}</th></tr></tfoot>
    </table>
    <div>Metode: {{ $transactionReturn->refund_method === 'transfer' ? 'Transfer' : 'Tunai' }}</div>
    @if($transactionReturn->reference)<div>Referensi: {{ $transactionReturn->reference }}</div>@endif
    <div>Alasan: {{ $transactionReturn->reason }}</div>
</div>
</body>
</html>
