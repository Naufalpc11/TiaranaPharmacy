<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeService extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'icon_image_path',
        'description',
        'items',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
