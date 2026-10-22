<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name', 'store_address', 'store_phone', 'receipt_footer',
        'landing_title', 'landing_text_highlight', 'landing_description', 'primary_color',
        'logo', 'landing_image', 'social_links', 'font_family',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }
}
