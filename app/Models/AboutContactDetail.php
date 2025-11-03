<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContactDetail extends Model
{
    protected $fillable = [
        'icon',
        'icon_image_path',
        'title',
        'lines',
        'copy_text',
        'sort_order',
    ];

    protected $casts = [
        'lines' => 'array',
    ];
}
