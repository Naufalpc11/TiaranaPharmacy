<?php

namespace App\Filament\Resources\HomeAboutFeatureResource\Pages;

use App\Filament\Resources\HomeAboutFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeAboutFeatures extends ListRecords
{
    protected static string $resource = HomeAboutFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
