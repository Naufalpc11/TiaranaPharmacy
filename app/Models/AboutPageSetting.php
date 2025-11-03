<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPageSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_background_image_path',
        'hero_primary_button_text',
        'hero_primary_button_url',
        'hero_secondary_button_text',
        'hero_secondary_button_url',
        'vision_title',
        'vision_text',
        'mission_title',
        'history_title',
        'history_description',
        'history_image_path',
        'team_title',
        'team_intro',
        'pharmacist_name',
        'pharmacist_role',
        'pharmacist_stra',
        'pharmacist_sipa',
        'pharmacist_schedule',
        'pharmacist_photo_path',
        'pharmacist_photo_alt',
        'pharmacist_badges',
        'location_title',
        'location_intro',
        'location_map_embed_url',
    ];

    protected $casts = [
        'pharmacist_badges' => 'array',
    ];
}
