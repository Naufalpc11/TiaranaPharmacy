<?php

namespace App\Filament\Resources\AboutHistoryStatResource\Pages;

use App\Filament\Resources\AboutHistoryStatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutHistoryStat extends EditRecord
{
    protected static string $resource = AboutHistoryStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
