<?php

namespace App\Support;

use Illuminate\Support\Str;

class AgeRange
{
    public const BAYI = 'bayi';
    public const ANAK_2_6 = 'anak-2-6';
    public const ANAK_6_12 = 'anak-6-12';
    public const ANAK_2_12 = 'anak-2-12';
    public const DEWASA = 'dewasa';

    public static function options(): array
    {
        return [
            self::BAYI => 'Bayi (0-2 tahun)',
            self::ANAK_2_6 => 'Anak 2-6 tahun',
            self::ANAK_6_12 => 'Anak 6-12 tahun',
            self::ANAK_2_12 => 'Anak 2-12 tahun',
            self::DEWASA => 'Dewasa (12+ tahun)',
        ];
    }

    public static function label(?string $value): string
    {
        return self::options()[$value] ?? Str::title((string) $value);
    }

    public static function normalize(?string $value): ?string
    {
        $key = Str::lower((string) $value);

        return match ($key) {
            'bayi', 'infant', '0-2', '0_2' => self::BAYI,
            'anak-2-6', 'anak 2-6', 'balita' => self::ANAK_2_6,
            'anak-6-12', 'anak 6-12' => self::ANAK_6_12,
            'anak-2-12', 'anak 2-12', 'anak', 'anak-anak' => self::ANAK_2_12,
            'dewasa', 'adult', '12+', '12-plus' => self::DEWASA,
            default => null,
        };
    }

    public static function bounds(?string $value): array
    {
        return match ($value) {
            self::BAYI => [0, 2],
            self::ANAK_2_6 => [2, 6],
            self::ANAK_6_12 => [6, 12],
            self::ANAK_2_12 => [2, 12],
            self::DEWASA => [12, null],
            default => [null, null],
        };
    }

    public static function fromYears(?float $years): ?string
    {
        if ($years === null) {
            return null;
        }

        if ($years < 2) {
            return self::BAYI;
        }

        if ($years < 6) {
            return self::ANAK_2_6;
        }

        if ($years < 12) {
            return self::ANAK_6_12;
        }

        return self::DEWASA;
    }

    public static function describe(?string $value): string
    {
        return self::label($value) ?: 'semua usia';
    }

    public static function detectFromText(?string $text): ?array
    {
        $content = Str::lower((string) $text);

        if ($content === '') {
            return null;
        }

        if (Str::contains($content, ['bayi', 'infant', '0-2'])) {
            return [
                'range' => self::BAYI,
                'raw' => 'bayi',
                'age_years' => null,
            ];
        }

        if (Str::contains($content, ['balita', 'toddlers', '2-6'])) {
            return [
                'range' => self::ANAK_2_6,
                'raw' => 'balita',
                'age_years' => null,
            ];
        }

        if (Str::contains($content, ['dewasa', 'adult'])) {
            return [
                'range' => self::DEWASA,
                'raw' => 'dewasa',
                'age_years' => null,
            ];
        }

        if (Str::contains($content, ['anak 6', '6-12', 'usia 7', 'usia 8', 'usia 9', 'usia 10', 'usia 11'])) {
            return [
                'range' => self::ANAK_6_12,
                'raw' => 'anak 6-12',
                'age_years' => null,
            ];
        }

        if (Str::contains($content, ['anak 2', '2-12', 'anak-anak'])) {
            return [
                'range' => self::ANAK_2_12,
                'raw' => 'anak 2-12',
                'age_years' => null,
            ];
        }

        if (preg_match('/(\d{1,2})\s*(tahun|th|yo)/u', $content, $matches)) {
            $years = (int) ($matches[1] ?? 0);

            return [
                'range' => self::fromYears($years),
                'raw' => trim($matches[0]),
                'age_years' => $years,
            ];
        }

        if (preg_match('/(\d{1,2})\s*(bulan|bln)/u', $content, $matches)) {
            $months = (int) ($matches[1] ?? 0);
            $years = $months / 12;

            return [
                'range' => $years < 2 ? self::BAYI : self::ANAK_2_6,
                'raw' => trim($matches[0]),
                'age_years' => $years,
            ];
        }

        return null;
    }

    public static function isCompatible(?string $medicationRange, ?string $requestedRange): bool
    {
        if (! $medicationRange || ! $requestedRange) {
            return true;
        }

        [$medMin, $medMax] = self::bounds($medicationRange);
        [$reqMin, $reqMax] = self::bounds($requestedRange);

        $medMin ??= $reqMin ?? 0;
        $reqMin ??= $medMin ?? 0;

        $medMaxValue = $medMax ?? 200;
        $reqMaxValue = $reqMax ?? 200;

        return $medMin <= $reqMaxValue && $reqMin <= $medMaxValue;
    }
}
