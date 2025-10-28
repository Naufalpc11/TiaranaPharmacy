<?php

namespace App\Filament\Resources\HomePageSettingResource\Pages;

use App\Filament\Resources\HomePageSettingResource;
use App\Models\HomePageSetting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomePageSettings extends ListRecords
{
    protected static string $resource = HomePageSettingResource::class;

    protected function getHeaderActions(): array
    {
        if (HomePageSetting::query()->exists()) {
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengaturan'),
        ];
    }
}
