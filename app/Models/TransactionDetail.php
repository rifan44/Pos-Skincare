<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id', 'product_id', 'quantity', 'price', 'discount_amount', 'hpp', 'subtotal',
    ];

    // Relasi ke tabel products (yang sudah ada)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // TAMBAHKAN RELASI INI: Relasi balik ke tabel transactions
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function returns()
    {
        return $this->hasMany(TransactionReturnDetail::class);
    }
}
