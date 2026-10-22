<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Setting;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
        ]);

        $query = Transaction::query();

        // Jika user memasukkan filter tanggal
        if ($request->start_date && $request->end_date) {
            $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Tampilkan hasil (terbaru di atas)
        $transactions = $query->with('returns')->latest()->get();
        
        return view('pages.history.index', compact('transactions'));
    }

    public function print($id)
    {
        // Mencari transaksi berdasarkan ID beserta detail barang yang dibeli
        $transaction = Transaction::with('details.product')->findOrFail($id);
        $setting = Setting::first();

        return view('pages.history.print', compact('transaction', 'setting'));
    }
}
