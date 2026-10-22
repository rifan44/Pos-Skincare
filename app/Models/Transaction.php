<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['total_amount', 'pay_amount'];

    // Satu transaksi memiliki banyak detail (banyak barang yang dibeli)
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function returns()
    {
        return $this->hasMany(TransactionReturn::class);
    }

    public function getRefundedAmountAttribute(): int
    {
        return $this->relationLoaded('returns')
            ? (int) $this->returns->sum('total_amount')
            : (int) $this->returns()->sum('total_amount');
    }

    public function getReturnStatusAttribute(): string
    {
        $refundedAmount = $this->refunded_amount;

        if ($refundedAmount <= 0) {
            return 'completed';
        }

        return $refundedAmount >= $this->total_amount ? 'refunded' : 'partially_refunded';
    }
}
