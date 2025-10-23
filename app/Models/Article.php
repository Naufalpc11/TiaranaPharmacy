<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'cover_image_url',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $article): void {
            if (blank($article->slug) && filled($article->title)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public function coverImageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->cover_image) {
                return null;
            }

            if (Str::startsWith($this->cover_image, ['http://', 'https://', 'data:'])) {
                return $this->cover_image;
            }

            return asset('storage/' . ltrim($this->cover_image, '/'));
        });
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
