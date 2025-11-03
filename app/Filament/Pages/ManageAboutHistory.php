<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AboutPageSettingResource;
use App\Models\AboutHistoryStat;
use App\Models\AboutPageSetting;
use Filament\Actions\Action;
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

class ManageAboutHistory extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'filament.pages.about-settings-form-table';

    protected static ?string $title = 'Sejarah';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Sejarah';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    protected ?int $recordId = null;

    public function mount(): void
    {
        $record = $this->resolveRecord();
        $this->form->fill($record->only([
            'history_title',
            'history_description',
            'history_image_path',
        ]));
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema(AboutPageSettingResource::getHistorySchema())
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

        $this->form->fill($record->only([
            'history_title',
            'history_description',
            'history_image_path',
        ]));

        Notification::make()
            ->title('Pengaturan sejarah berhasil disimpan.')
            ->success()
            ->send();
    }

    protected function getTableQuery()
    {
        return AboutHistoryStat::query()->orderBy('sort_order');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('icon')
                ->label('Ikon')
                ->limit(40)
                ->toggleable(),
            TextColumn::make('value')
                ->label('Nilai')
                ->sortable()
                ->searchable(),
            TextColumn::make('label')
                ->label('Label')
                ->sortable()
                ->searchable(),
            TextColumn::make('sort_order')
                ->label('Urutan')
                ->sortable(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Statistik')
                ->createAnother(false)
                ->form([
                    Forms\Components\TextInput::make('icon')
                        ->label('Ikon')
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('icon_image_path')
                        ->label('Ikon Gambar')
                        ->directory('about/history/icons')
                        ->disk('public')
                        ->image()
                        ->maxSize(5120)
                        ->imageResizeTargetWidth(128)
                        ->imageResizeTargetHeight(128)
                        ->helperText('Upload ikon PNG/SVG/JPG maks. 5 MB. Disarankan ukuran 128x128 px.')
                        ->previewable()
                        ->downloadable()
                        ->openable(),
                    Forms\Components\TextInput::make('value')
                        ->label('Nilai')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(fn () => (AboutHistoryStat::max('sort_order') ?? 0) + 1),
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit')
                ->form([
                    Forms\Components\TextInput::make('icon')
                        ->label('Ikon')
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('icon_image_path')
                        ->label('Ikon Gambar')
                        ->directory('about/history/icons')
                        ->disk('public')
                        ->image()
                        ->maxSize(5120)
                        ->imageResizeTargetWidth(128)
                        ->imageResizeTargetHeight(128)
                        ->helperText('Upload ikon PNG/SVG/JPG maks. 5 MB. Disarankan ukuran 128x128 px.')
                        ->previewable()
                        ->downloadable()
                        ->openable(),
                    Forms\Components\TextInput::make('value')
                        ->label('Nilai')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric(),
                ]),
            DeleteAction::make()->label('Hapus'),
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
