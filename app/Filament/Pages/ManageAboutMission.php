<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AboutPageSettingResource;
use App\Models\AboutMission;
use App\Models\AboutPageSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageAboutMission extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'filament.pages.about-settings-form-table';

    protected static ?string $title = 'Misi';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Misi';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    protected ?int $recordId = null;

    public function mount(): void
    {
        $record = $this->resolveRecord();
        $this->form->fill($record->only(['mission_title']));
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema(AboutPageSettingResource::getMissionSchema())
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

        $this->form->fill($record->only(['mission_title']));

        Notification::make()
            ->title('Pengaturan misi berhasil disimpan.')
            ->success()
            ->send();
    }

    protected function getTableQuery()
    {
        return AboutMission::query()->orderBy('sort_order');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->label('Judul')
                ->limit(40)
                ->sortable()
                ->searchable(),
            TextColumn::make('description')
                ->label('Deskripsi')
                ->limit(80)
                ->toggleable(),
            TextColumn::make('sort_order')
                ->label('Urutan')
                ->sortable()
                ->toggleable(),
            TextColumn::make('updated_at')
                ->label('Diperbarui')
                ->dateTime('d M Y H:i')
                ->since()
                ->sortable()
                ->toggleable(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Misi')
                ->createAnother(false)
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(4),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(fn () => (AboutMission::max('sort_order') ?? 0) + 1),
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit')
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(4),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(fn (AboutMission $record) => $record->sort_order ?? 0),
                ]),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->headerActions($this->getTableHeaderActions())
            ->actions($this->getTableActions())
            ->reorderable('sort_order');
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
