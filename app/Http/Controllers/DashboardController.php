<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionReturn;
use App\Services\TransactionFinancialSummary;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, TransactionFinancialSummary $financialSummary)
    {
        $request->validate([
            'filter_date' => 'nullable|date',
        ]);

        $filterDate = $request->input('filter_date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($filterDate);
        $startDate = $selectedDate->copy()->startOfDay();
        $endDate = $selectedDate->copy()->endOfDay();
        $title = 'Ringkasan ' . $selectedDate->isoFormat('D MMMM Y');

        $transactions = Transaction::with('details')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $returns = TransactionReturn::with('details')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $summary = $financialSummary->summarize($transactions, $returns);
        $totalOmset = $summary['total_omzet'];
        $totalRefund = $summary['total_refund'];
        $totalTransaksi = $summary['total_transaksi'];
        $totalLaba = $summary['total_laba'];
        $hppBelumDiketahui = $summary['hpp_belum_diketahui'];

        $stokMenipis = Product::with('category')->where('stock', '<', 10)->get();

        return view('pages.dashboard.index', compact(
            'totalOmset', 'totalRefund', 'totalLaba', 'hppBelumDiketahui', 'totalTransaksi', 'stokMenipis', 'filterDate', 'title'
        ));
    }
}
