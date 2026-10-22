<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['subtotal_amount', 'discount_amount', 'total_amount', 'pay_amount'];

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            if (! array_key_exists('subtotal_amount', $transaction->getAttributes())) {
                $transaction->subtotal_amount = (int) $transaction->total_amount + (int) ($transaction->discount_amount ?? 0);
            }
        });
    }

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
