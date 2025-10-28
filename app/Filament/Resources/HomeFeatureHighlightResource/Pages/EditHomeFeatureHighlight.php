<?php

namespace App\Filament\Resources\HomeFeatureHighlightResource\Pages;

use App\Filament\Resources\HomeFeatureHighlightResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeFeatureHighlight extends EditRecord
{
    protected static string $resource = HomeFeatureHighlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
