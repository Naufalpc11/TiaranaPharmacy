<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AboutPageSettingResource;
use App\Models\AboutPageSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAboutTeam extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.about-settings-form';

    protected static ?string $title = 'Tim Apoteker';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Tim Apoteker';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    protected ?int $recordId = null;

    public function mount(): void
    {
        $record = $this->resolveRecord();
        $this->form->fill($record->only([
            'team_title',
            'team_intro',
            'pharmacist_name',
            'pharmacist_role',
            'pharmacist_stra',
            'pharmacist_sipa',
            'pharmacist_schedule',
            'pharmacist_badges',
            'pharmacist_photo_path',
            'pharmacist_photo_alt',
        ]));
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema(AboutPageSettingResource::getTeamSchema())
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

        $this->form->fill($record->only([
            'team_title',
            'team_intro',
            'pharmacist_name',
            'pharmacist_role',
            'pharmacist_stra',
            'pharmacist_sipa',
            'pharmacist_schedule',
            'pharmacist_badges',
            'pharmacist_photo_path',
            'pharmacist_photo_alt',
        ]));

        Notification::make()
            ->title('Pengaturan tim berhasil disimpan.')
            ->success()
            ->send();
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
