<?php

namespace App\Filament\Resources\AboutHistoryStatResource\Pages;

use App\Filament\Resources\AboutHistoryStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAboutHistoryStats extends ListRecords
{
    protected static string $resource = AboutHistoryStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
