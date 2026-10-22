<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionReturn extends Model
{
    protected $fillable = [
        'transaction_id',
        'processed_by',
        'processed_by_name',
        'reason',
        'total_amount',
        'refund_method',
        'reference',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function details()
    {
        return $this->hasMany(TransactionReturnDetail::class);
    }
}
