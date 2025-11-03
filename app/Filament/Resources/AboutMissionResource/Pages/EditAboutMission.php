<?php

namespace App\Filament\Resources\AboutMissionResource\Pages;

use App\Filament\Resources\AboutMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutMission extends EditRecord
{
    protected static string $resource = AboutMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
