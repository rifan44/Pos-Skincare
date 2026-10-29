<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $setting->store_name ?? 'Toko' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f7f3ef; color: #3b3331; }
        .summary-box { border: 1px solid #ddd; padding: 15px; margin-top: 20px; text-align: right; background-color: #fdfbf7; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2 style="margin-bottom: 5px;">{{ $setting->store_name ?? 'POS Skincare' }}</h2>
        <p style="margin-top: 0; color: #666;">Laporan Penjualan dan Retur</p>
        <hr style="border-top: 1px solid #ddd;">
        @if($startDate && $endDate)
        <p class="fw-bold">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @endif
    </div>

    @if($hppBelumDiketahui > 0)
    <p>HPP belum tercatat untuk {{ $hppBelumDiketahui }} unit penjualan atau retur. Laba kotor hanya mencakup barang dengan HPP tersimpan.</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Tanggal Penjualan</th>
                <th class="text-right">Omzet Kotor</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Refund</th>
                <th class="text-right">Omzet Bersih</th>
                <th class="text-right">Laba Kotor Tercatat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyReports as $report)
            <tr>
                <td>{{ \Carbon\Carbon::parse($report['date'])->format('d M Y') }}</td>
                <td class="text-right">Rp {{ number_format($report['gross_omzet'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($report['discounts'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($report['refunds'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($report['omzet'], 0, ',', '.') }}</td>
                <td class="text-right fw-bold" style="color: green;">Rp {{ number_format($report['laba'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada transaksi atau retur pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Omzet Kotor: Rp {{ number_format($grossOmzet, 0, ',', '.') }}</h3>
        <h3>Total Diskon: Rp {{ number_format($totalDiskon, 0, ',', '.') }}</h3>
        <h3>Total Refund: Rp {{ number_format($totalRefund, 0, ',', '.') }}</h3>
        <h3>Omzet Bersih: Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
        <h3 style="color: green;">Total Laba Kotor Tercatat: Rp {{ number_format($totalLaba, 0, ',', '.') }}</h3>
    </div>
</body>
</html>
