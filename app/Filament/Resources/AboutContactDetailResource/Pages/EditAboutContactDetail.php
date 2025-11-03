<?php

namespace App\Filament\Resources\AboutContactDetailResource\Pages;

use App\Filament\Resources\AboutContactDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutContactDetail extends EditRecord
{
    protected static string $resource = AboutContactDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
