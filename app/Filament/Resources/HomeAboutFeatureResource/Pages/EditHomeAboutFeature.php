<?php

namespace App\Filament\Resources\HomeAboutFeatureResource\Pages;

use App\Filament\Resources\HomeAboutFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeAboutFeature extends EditRecord
{
    protected static string $resource = HomeAboutFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
