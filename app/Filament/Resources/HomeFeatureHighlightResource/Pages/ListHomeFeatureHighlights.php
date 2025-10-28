<?php

namespace App\Filament\Resources\HomeFeatureHighlightResource\Pages;

use App\Filament\Resources\HomeFeatureHighlightResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeFeatureHighlights extends ListRecords
{
    protected static string $resource = HomeFeatureHighlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
