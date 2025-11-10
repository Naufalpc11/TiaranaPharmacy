<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Storage;

class BugReport extends Model
{
    use Prunable;

    public const STATUS_NEW = 'baru';
    public const STATUS_IN_PROGRESS = 'proses';
    public const STATUS_DONE = 'selesai';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject',
        'email',
        'description',
        'status',
        'screenshot_path',
        'screenshot_original_name',
        'is_resolved',
        'resolved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        return static::where('created_at', '<', now()->subDays(60))->where('is_resolved', true);
    }

    /**
     * Automatically set the resolved timestamp when the status changes.
     */
    protected static function booted(): void
    {
        static::saving(function (self $report) {
            if ($report->isDirty('status')) {
                $report->is_resolved = $report->status === self::STATUS_DONE;
                $report->resolved_at = $report->is_resolved ? now() : null;
            }

            if ($report->isDirty('is_resolved')) {
                $report->resolved_at = $report->is_resolved ? now() : null;

                if ($report->is_resolved && $report->status !== self::STATUS_DONE) {
                    $report->status = self::STATUS_DONE;
                }

                if (! $report->is_resolved && $report->status === self::STATUS_DONE) {
                    $report->status = self::STATUS_IN_PROGRESS;
                }
            }
        });

        static::deleting(function (self $report) {
            if ($report->screenshot_path) {
                Storage::disk('public')->delete($report->screenshot_path);
            }
        });
    }

    /**
     * Status labels for UI options.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW => 'Baru',
            self::STATUS_IN_PROGRESS => 'Proses',
            self::STATUS_DONE => 'Selesai',
        ];
    }
}
