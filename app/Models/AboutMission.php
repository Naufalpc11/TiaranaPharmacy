<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutMission extends Model
{
    protected $fillable = [
        'title',
        'description',
        'sort_order',
    ];
}
