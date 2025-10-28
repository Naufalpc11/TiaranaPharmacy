<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeAboutFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'icon_image_path',
        'sort_order',
    ];
}
