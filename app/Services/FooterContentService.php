<?php

namespace App\Services;

use App\Models\FooterSetting;

class FooterContentService
{
    private const DEFAULT_TAGLINE = 'Melayani dengan sepenuh hati sejak 2010';

    private const DEFAULT_CONTACT = [
        'phone' => '0812-3456-7890',
        'email' => 'tiaranafarma@gmail.com',
        'address' => 'Jl. Sepinggan Baru',
    ];

    private const DEFAULT_HOURS = [
        'weekday' => 'Senin - Sabtu: 08:00 - 22:00',
        'weekend' => 'Minggu: 09:00 - 22:00',
    ];

    private const DEFAULT_SOCIAL_LINKS = [
        'facebook' => '#',
        'instagram' => '#',
        'whatsapp' => '#',
    ];

    public function get(): array
    {
        try {
            /** @var FooterSetting|null $setting */
            $setting = FooterSetting::query()->first();

            if (! $setting) {
                $setting = FooterSetting::query()->create([
                    'tagline' => self::DEFAULT_TAGLINE,
                    'contact_phone' => self::DEFAULT_CONTACT['phone'],
                    'contact_email' => self::DEFAULT_CONTACT['email'],
                    'contact_address' => self::DEFAULT_CONTACT['address'],
                    'operational_hours_primary' => self::DEFAULT_HOURS['weekday'],
                    'operational_hours_secondary' => self::DEFAULT_HOURS['weekend'],
                    'facebook_url' => self::DEFAULT_SOCIAL_LINKS['facebook'],
                    'instagram_url' => self::DEFAULT_SOCIAL_LINKS['instagram'],
                    'whatsapp_url' => self::DEFAULT_SOCIAL_LINKS['whatsapp'],
                ]);
            }
        } catch (\Throwable $exception) {
            report($exception);
            $setting = null;
        }

        return $this->buildResponse($setting);
    }

    protected function buildResponse(?FooterSetting $setting): array
    {
        return [
            'tagline' => $setting?->tagline ?? self::DEFAULT_TAGLINE,
            'contact' => [
                'phone' => $setting?->contact_phone ?? self::DEFAULT_CONTACT['phone'],
                'email' => $setting?->contact_email ?? self::DEFAULT_CONTACT['email'],
                'address' => $setting?->contact_address ?? self::DEFAULT_CONTACT['address'],
            ],
            'hours' => [
                'weekday' => $setting?->operational_hours_primary ?? self::DEFAULT_HOURS['weekday'],
                'weekend' => $setting?->operational_hours_secondary ?? self::DEFAULT_HOURS['weekend'],
            ],
            'social_links' => [
                'facebook' => $setting?->facebook_url ?: self::DEFAULT_SOCIAL_LINKS['facebook'],
                'instagram' => $setting?->instagram_url ?: self::DEFAULT_SOCIAL_LINKS['instagram'],
                'whatsapp' => $setting?->whatsapp_url ?: self::DEFAULT_SOCIAL_LINKS['whatsapp'],
            ],
        ];
    }
}
