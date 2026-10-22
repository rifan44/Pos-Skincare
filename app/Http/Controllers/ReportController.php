<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionReturn;
use App\Models\Setting;
use App\Services\TransactionFinancialSummary;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, TransactionFinancialSummary $financialSummary)
    {
        $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::with('details')->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $transactions = $query->get();
        $returnsQuery = TransactionReturn::with('details')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $returnsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        $returns = $returnsQuery->get();

        $summary = $financialSummary->summarize($transactions, $returns);
        $dailyReports = $summary['daily_reports'];
        $totalOmzet = $summary['total_omzet'];
        $grossOmzet = $summary['gross_omzet'];
        $totalRefund = $summary['total_refund'];
        $totalModal = $summary['total_modal'];
        $totalLaba = $summary['total_laba'];
        $totalItem = $summary['total_item'];
        $hppBelumDiketahui = $summary['hpp_belum_diketahui'];

        return view('pages.reports.index', compact(
            'dailyReports', 'totalOmzet', 'grossOmzet', 'totalRefund', 'totalModal', 'totalLaba', 'totalItem',
            'hppBelumDiketahui', 'startDate', 'endDate'
        ));
    }

    public function exportPdf(Request $request, TransactionFinancialSummary $financialSummary)
    {
        $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::with('details')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        $transactions = $query->get();
        $returnsQuery = TransactionReturn::with('details')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $returnsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        $returns = $returnsQuery->get();

        $summary = $financialSummary->summarize($transactions, $returns);
        $dailyReports = $summary['daily_reports'];
        $totalOmzet = $summary['total_omzet'];
        $grossOmzet = $summary['gross_omzet'];
        $totalRefund = $summary['total_refund'];
        $totalLaba = $summary['total_laba'];
        $hppBelumDiketahui = $summary['hpp_belum_diketahui'];
        $setting = Setting::first();

        return view('pages.reports.pdf', compact(
            'dailyReports', 'totalOmzet', 'grossOmzet', 'totalRefund', 'totalLaba', 'hppBelumDiketahui',
            'startDate', 'endDate', 'setting'
        ));
    }
}
