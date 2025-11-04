<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseUsage
{
    /**
     * Get database usage details relative to the configured maximum size.
     *
     * @return array{used_bytes:int, max_bytes:int, percentage:float}|null
     */
    public function getUsage(): ?array
    {
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        $maxSizeMb = (int) Config::get('database-usage.max_size_mb', 0);
        $thresholdBytes = $maxSizeMb > 0 ? $maxSizeMb * 1024 * 1024 : 0;

        if ($thresholdBytes <= 0) {
            return null;
        }

        if ($driver !== 'mysql') {
            return null;
        }

        $database = DB::connection($connection)->getDatabaseName();

        $usage = DB::connection($connection)->selectOne(
            <<<SQL
            SELECT
                COALESCE(SUM(data_length + index_length), 0) AS used_bytes
            FROM information_schema.TABLES
            WHERE table_schema = ?
            SQL,
            [$database]
        );

        if (! $usage) {
            return null;
        }

        $usedBytes = (int) ($usage->used_bytes ?? 0);

        return [
            'used_bytes' => $usedBytes,
            'max_bytes' => $thresholdBytes,
            'percentage' => $thresholdBytes > 0 ? ($usedBytes / $thresholdBytes) * 100 : 0.0,
        ];
    }

    /**
     * Format usage data for display.
     */
    public function getFormattedUsage(): ?array
    {
        $usage = $this->getUsage();

        if (! $usage) {
            return null;
        }

        $usedMb = $usage['used_bytes'] / (1024 * 1024);
        $maxMb = $usage['max_bytes'] / (1024 * 1024);

        return [
            'percentage' => $usage['percentage'],
            'percentage_formatted' => number_format($usage['percentage'], 2),
            'used_mb' => $usedMb,
            'used_mb_formatted' => number_format($usedMb, 2),
            'max_mb' => $maxMb,
            'max_mb_formatted' => number_format($maxMb, 2),
        ];
    }
}
