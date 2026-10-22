<?php

namespace App\Services;

use Illuminate\Support\Collection;

class TransactionFinancialSummary
{
    /**
     * Summarize committed transactions using the HPP snapshot stored per detail.
     * A zero HPP is treated as unknown because the column migration defaulted old rows to zero.
     */
    public function summarize(Collection $transactions, ?Collection $returns = null): array
    {
        $returns ??= collect();

        $summary = [
            'gross_omzet' => 0,
            'total_refund' => 0,
            'total_omzet' => 0,
            'total_modal' => 0,
            'total_laba' => 0,
            'total_transaksi' => $transactions->count(),
            'total_item' => 0,
            'total_item_diretur' => 0,
            'hpp_belum_diketahui' => 0,
            'daily_reports' => collect(),
        ];

        $daily = [];

        foreach ($transactions as $transaction) {
            $omzet = (int) $transaction->total_amount;

            $date = $transaction->created_at->toDateString();
            $daily[$date] ??= [
                'date' => $date,
                'total_transactions' => 0,
                'total_items' => 0,
                'refunds' => 0,
                'total_refunds' => 0,
                'returned_items' => 0,
                'modal' => 0,
                'gross_omzet' => 0,
                'laba' => 0,
                'hpp_belum_diketahui' => 0,
            ];
            $daily[$date]['total_transactions']++;
            $daily[$date]['gross_omzet'] += $omzet;

            foreach ($transaction->details as $detail) {
                $quantity = (int) $detail->quantity;
                $hpp = (int) $detail->hpp;
                $summary['total_item'] += $quantity;
                $daily[$date]['total_items'] += $quantity;

                if ($hpp <= 0) {
                    $summary['hpp_belum_diketahui'] += $quantity;
                    $daily[$date]['hpp_belum_diketahui'] += $quantity;
                    continue;
                }

                $modal = $hpp * $quantity;
                $laba = (int) $detail->subtotal - $modal;
                $summary['total_modal'] += $modal;
                $summary['total_laba'] += $laba;
                $daily[$date]['modal'] += $modal;
                $daily[$date]['laba'] += $laba;
            }
        }

        foreach ($returns as $transactionReturn) {
            $date = $transactionReturn->created_at->toDateString();
            $daily[$date] ??= [
                'date' => $date,
                'total_transactions' => 0,
                'total_items' => 0,
                'refunds' => 0,
                'total_refunds' => 0,
                'returned_items' => 0,
                'modal' => 0,
                'gross_omzet' => 0,
                'laba' => 0,
                'hpp_belum_diketahui' => 0,
            ];

            $refundAmount = (int) $transactionReturn->total_amount;
            $summary['total_refund'] += $refundAmount;
            $daily[$date]['refunds'] += $refundAmount;
            $daily[$date]['total_refunds']++;

            foreach ($transactionReturn->details as $detail) {
                $quantity = (int) $detail->quantity;
                $hpp = (int) $detail->hpp_snapshot;
                $summary['total_item_diretur'] += $quantity;
                $daily[$date]['returned_items'] += $quantity;

                if ($hpp <= 0) {
                    $summary['hpp_belum_diketahui'] += $quantity;
                    $daily[$date]['hpp_belum_diketahui'] += $quantity;
                    continue;
                }

                $restockedCost = $hpp * (int) $detail->restocked_quantity;
                $summary['total_modal'] -= $restockedCost;
                $summary['total_laba'] += $restockedCost - (int) $detail->refund_amount;
                $daily[$date]['modal'] -= $restockedCost;
                $daily[$date]['laba'] += $restockedCost - (int) $detail->refund_amount;
            }
        }

        $summary['gross_omzet'] = collect($daily)->sum('gross_omzet');
        $summary['total_omzet'] = $summary['gross_omzet'] - $summary['total_refund'];
        foreach ($daily as &$row) {
            $row['omzet'] = $row['gross_omzet'] - $row['refunds'];
        }
        unset($row);

        $summary['daily_reports'] = collect($daily)->sortKeysDesc()->values();

        return $summary;
    }
}
