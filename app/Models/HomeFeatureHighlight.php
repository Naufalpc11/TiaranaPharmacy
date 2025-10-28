<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeFeatureHighlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'description',
        'sort_order',
    ];
}
