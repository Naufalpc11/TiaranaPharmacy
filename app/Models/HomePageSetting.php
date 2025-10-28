<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle_primary',
        'hero_subtitle_secondary',
        'hero_background_image_path',
        'about_title',
        'about_description',
        'about_image_path',
    ];
}
