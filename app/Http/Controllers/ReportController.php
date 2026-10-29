<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ReportDataService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request, ReportDataService $reportData)
    {
        $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $summary = $reportData->summarize($startDate, $endDate);
        $dailyReports = $summary['daily_reports'];
        $totalOmzet = $summary['total_omzet'];
        $grossOmzet = $summary['gross_omzet'];
        $totalDiskon = $summary['total_discount'];
        $totalRefund = $summary['total_refund'];
        $totalModal = $summary['total_modal'];
        $totalLaba = $summary['total_laba'];
        $totalItem = $summary['total_item'];
        $hppBelumDiketahui = $summary['hpp_belum_diketahui'];

        return view('pages.reports.index', compact(
            'dailyReports', 'totalOmzet', 'grossOmzet', 'totalDiskon', 'totalRefund', 'totalModal', 'totalLaba', 'totalItem',
            'hppBelumDiketahui', 'startDate', 'endDate'
        ));
    }

    public function exportPdf(Request $request, ReportDataService $reportData)
    {
        $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $summary = $reportData->summarize($startDate, $endDate);
        $dailyReports = $summary['daily_reports'];
        $totalOmzet = $summary['total_omzet'];
        $grossOmzet = $summary['gross_omzet'];
        $totalDiskon = $summary['total_discount'];
        $totalRefund = $summary['total_refund'];
        $totalLaba = $summary['total_laba'];
        $hppBelumDiketahui = $summary['hpp_belum_diketahui'];
        $setting = Setting::first();

        return Pdf::loadView('pages.reports.pdf', compact(
            'dailyReports', 'totalOmzet', 'grossOmzet', 'totalDiskon', 'totalRefund', 'totalLaba', 'hppBelumDiketahui',
            'startDate', 'endDate', 'setting'
        ))->download('laporan-penjualan-'.$startDate.'-sampai-'.$endDate.'.pdf');
    }

    public function exportCsv(Request $request, ReportDataService $reportData): StreamedResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $summary = $reportData->summarize($startDate, $endDate);
        $filename = 'laporan-penjualan-'.$startDate.'-sampai-'.$endDate.'.csv';

        return response()->streamDownload(function () use ($summary, $startDate, $endDate): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Laporan Penjualan dan Retur']);
            fputcsv($handle, ['Periode', $startDate, 'sampai', $endDate]);
            fputcsv($handle, []);
            fputcsv($handle, ['Tanggal', 'Transaksi', 'Unit Terjual', 'Unit Retur', 'Diskon', 'Refund', 'HPP Bersih', 'Omzet Bersih', 'Laba Kotor']);

            foreach ($summary['daily_reports'] as $report) {
                fputcsv($handle, [
                    $report['date'],
                    $report['total_transactions'],
                    $report['total_items'],
                    $report['returned_items'],
                    $report['discounts'],
                    $report['refunds'],
                    $report['modal'],
                    $report['omzet'],
                    $report['laba'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['TOTAL', '', $summary['total_item'], $summary['total_item_diretur'], $summary['total_discount'], $summary['total_refund'], $summary['total_modal'], $summary['total_omzet'], $summary['total_laba']]);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
