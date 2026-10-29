<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionReturn;
use Illuminate\Support\Carbon;

class ReportDataService
{
    public function __construct(private TransactionFinancialSummary $financialSummary)
    {
    }

    public function summarize(?string $startDate, ?string $endDate): array
    {
        $transactions = Transaction::query()
            ->with('details')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            })
            ->orderByDesc('created_at')
            ->get();

        $returns = TransactionReturn::query()
            ->with('details')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            })
            ->orderByDesc('created_at')
            ->get();

        return $this->financialSummary->summarize($transactions, $returns);
    }
}
