<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</title>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #000;
            background-color: #fff;
        }
        
        .ticket {
            box-sizing: border-box;
            width: 100%;
            max-width: 80mm;
            padding: 3mm;
            margin: 0 auto;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .header {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        td { padding: 2px 0; vertical-align: top; }
        
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
        }

        /* Saat diprint, sembunyikan semua elemen selain struk */
        @media print {
            @page { margin: 0; }
            body { margin: 0; }
            .hidden-print, .screen-only { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="ticket">
        <!-- HEADER STRUK -->
        <div class="text-center">
            <h3 style="margin:0;">{{ $setting?->store_name ?? 'Skincare POS' }}</h3>
            @if($setting?->store_address)
                <p style="margin:0; font-size: 12px;">{{ $setting->store_address }}</p>
            @endif
            @if($setting?->store_phone)
                <p style="margin:0; font-size: 12px;">Telp: {{ $setting->store_phone }}</p>
            @endif
        </div>
        <hr>

        <div style="margin-bottom: 10px;">
            <table style="margin-bottom: 0;">
                <tr>
                    <td>Waktu</td>
                    <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>No. TRX</td>
                    <td class="text-right">#TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr class="border-bottom border-top">
                    <th style="text-align:left; padding:4px 0;">Item</th>
                    <th style="text-align:center; padding:4px 0;">Qty</th>
                    <th style="text-align:right; padding:4px 0;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                <tr>
                    <td colspan="3" class="fw-bold" style="padding-top:4px;">{{ $detail->product->name ?? 'Produk Terhapus' }}</td>
                </tr>
                <tr>
                    <td>@ {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-center">x{{ $detail->quantity }}</td>
                    <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <tr class="border-top">
                <td class="fw-bold" style="padding-top:5px;">TOTAL</td>
                <td class="text-right fw-bold" style="padding-top:5px;">{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>TUNAI</td>
                <td class="text-right">{{ number_format($transaction->pay_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>KEMBALI</td>
                <td class="text-right">{{ number_format($transaction->pay_amount - $transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer border-top">
            @if($setting?->receipt_footer)
                <p style="margin: 5px 0 2px;">{{ $setting->receipt_footer }}</p>
            @endif
            <p style="margin: 5px 0 2px;">Terima kasih atas kunjungannya!</p>
        </div>
    </div>

    <!-- Script agar halaman otomatis membuka dialog Print saat dimuat -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
