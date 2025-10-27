<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\Prunable;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use Prunable;

    /**
     * Fields that can be mass-assigned.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_reviewed',
        'reviewed_at',
    ];

    /**
     * Attribute casting definitions.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Scope for records eligible for pruning.
     */
    public function prunable()
    {
        return static::where('created_at', '<', now()->subDays(15));
    }

    /**
     * Automatically update review timestamps when status changes.
     */
    protected static function booted(): void
    {
        static::saving(function (self $message) {
            if ($message->isDirty('is_reviewed')) {
                $message->reviewed_at = $message->is_reviewed ? now() : null;
            }
        });
    }
}
