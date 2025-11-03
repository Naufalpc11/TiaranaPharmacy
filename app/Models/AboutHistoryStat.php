<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutHistoryStat extends Model
{
    protected $fillable = [
        'icon',
        'icon_image_path',
        'value',
        'label',
        'sort_order',
    ];
}
