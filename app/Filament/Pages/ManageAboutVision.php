<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AboutPageSettingResource;
use App\Models\AboutPageSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAboutVision extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.about-settings-form';

    protected static ?string $title = 'Visi';

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Visi';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    protected ?int $recordId = null;

    public function mount(): void
    {
        $record = $this->resolveRecord();
        $this->form->fill($this->formStateFromRecord($record));
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema(AboutPageSettingResource::getVisionSchema())
                ->columns(2)
                ->statePath('data')
                ->model($this->resolveRecord()),
        ];
    }

    public function save(): void
    {
        $record = $this->resolveRecord();
        $record->fill($this->form->getState());
        $record->save();
        $record->refresh();

        $this->form->fill($this->formStateFromRecord($record));

        Notification::make()
            ->title('Visi berhasil disimpan.')
            ->success()
            ->send();
    }

    protected function formStateFromRecord(AboutPageSetting $record): array
    {
        return $record->only([
            'vision_title',
            'vision_text',
        ]);
    }

    protected function resolveRecord(): AboutPageSetting
    {
        if ($this->recordId !== null) {
            if ($existing = AboutPageSetting::find($this->recordId)) {
                return $existing;
            }
        }

        $record = AboutPageSetting::query()->first();

        if (! $record) {
            $record = AboutPageSetting::query()->create([]);
        }

        $this->recordId = $record->getKey();

        return $record;
    }
}
