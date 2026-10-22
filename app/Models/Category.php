<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini

class Category extends Model
{
    use SoftDeletes; // Tambahkan ini

    protected $fillable = ['name', 'category_type'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function brandProducts()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
