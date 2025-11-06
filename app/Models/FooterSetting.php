<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'tagline',
        'contact_phone',
        'contact_email',
        'contact_address',
        'operational_hours_primary',
        'operational_hours_secondary',
        'facebook_url',
        'instagram_url',
        'whatsapp_url',
    ];
}
