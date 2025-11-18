<?php

namespace App\Filament\Resources\MedicationAssetResource\Pages;

use App\Filament\Resources\MedicationAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicationAssets extends ListRecords
{
    protected static string $resource = MedicationAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
