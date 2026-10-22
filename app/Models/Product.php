<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini

class Product extends Model
{
    use SoftDeletes; // Tambahkan ini

    protected $fillable = [
        'category_id',
        'brand_id',
        'sku',
        'name',
        'image',
        'purchase_price',
        'margin_percent',
        'selling_price',
        'stock',
        'expired_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Category::class, 'brand_id');
    }
}
