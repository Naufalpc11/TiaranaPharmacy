<?php

namespace App\Models;

use App\Support\AgeRange;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicationAsset extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'function_label',
        'form',
        'age_label',
        'age_min_years',
        'age_max_years',
        'image_path',
        'image_disk',
        'notes',
    ];

    protected $casts = [
        'age_min_years' => 'integer',
        'age_max_years' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (MedicationAsset $asset) {
            $asset->slug = $asset->generateSlug($asset->slug ?: $asset->name);

            [$min, $max] = AgeRange::bounds($asset->age_label);
            $asset->age_min_years = $min;
            $asset->age_max_years = $max;
            $asset->image_disk = $asset->image_disk ?: 'public';
        });
    }

    protected function generateSlug(?string $source): string
    {
        $base = Str::slug($source ?: 'medication');

        if ($base === '') {
            $base = 'medication';
        }

        $slug = $base;
        $counter = 1;

        while (
            static::where('slug', $slug)
                ->when($this->exists, fn ($query) => $query->where('id', '!=', $this->id))
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk($this->image_disk ?: 'public')->url($this->image_path);
    }
}
