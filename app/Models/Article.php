<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

            return $this->toRelativeStorageUrl($this->cover_image);
        });
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    protected function toRelativeStorageUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }

        $url = Storage::disk('public')->url($path);
        $appUrl = rtrim(config('app.url') ?? '', '/');

        if ($appUrl && Str::startsWith($url, $appUrl)) {
            return Str::after($url, $appUrl) ?: '/';
        }

        return $url;
    }
}
