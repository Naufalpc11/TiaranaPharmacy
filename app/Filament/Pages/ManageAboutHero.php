<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AboutPageSettingResource;
use App\Models\AboutPageSetting;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAboutHero extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.about-settings-form';

    protected static ?string $title = 'Hero';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Hero';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 1;

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
                ->schema(AboutPageSettingResource::getHeroSchema())
                ->columns(2)
                ->statePath('data')
                ->model($this->resolveRecord()),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save')
                ->color('primary'),
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
            ->title('Pengaturan hero berhasil disimpan.')
            ->success()
            ->send();
    }

    protected function formStateFromRecord(AboutPageSetting $record): array
    {
        return $record->only([
            'hero_title',
            'hero_subtitle',
            'hero_primary_button_text',
            'hero_primary_button_url',
            'hero_secondary_button_text',
            'hero_secondary_button_url',
            'hero_background_image_path',
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
