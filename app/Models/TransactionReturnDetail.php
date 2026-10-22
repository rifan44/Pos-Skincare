<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionReturnDetail extends Model
{
    protected $fillable = [
        'transaction_return_id',
        'transaction_detail_id',
        'quantity',
        'restocked_quantity',
        'unit_price_snapshot',
        'hpp_snapshot',
        'refund_amount',
    ];

    public function transactionReturn()
    {
        return $this->belongsTo(TransactionReturn::class);
    }

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class);
    }
}
