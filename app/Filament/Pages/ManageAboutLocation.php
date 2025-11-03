<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AboutPageSettingResource;
use App\Models\AboutContactDetail;
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

class ManageAboutLocation extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'filament.pages.about-settings-form-table';

    protected static ?string $title = 'Lokasi';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Lokasi';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    protected ?int $recordId = null;

    public function mount(): void
    {
        $record = $this->resolveRecord();
        $this->form->fill($record->only([
            'location_title',
            'location_intro',
            'location_map_embed_url',
        ]));
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema(AboutPageSettingResource::getLocationSchema())
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
            'location_title',
            'location_intro',
            'location_map_embed_url',
        ]));

        Notification::make()
            ->title('Pengaturan lokasi berhasil disimpan.')
            ->success()
            ->send();
    }

    protected function getTableQuery()
    {
        return AboutContactDetail::query()->orderBy('sort_order');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->label('Judul')
                ->sortable()
                ->searchable(),
            TextColumn::make('icon')
                ->label('Ikon')
                ->limit(40)
                ->toggleable(),
            TextColumn::make('sort_order')
                ->label('Urutan')
                ->sortable(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Info Kontak')
                ->createAnother(false)
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('icon')
                        ->label('Ikon')
                        ->maxLength(255)
                        ->helperText('Gunakan kelas Font Awesome. Kosongkan jika menggunakan ikon gambar.'),
                    Forms\Components\FileUpload::make('icon_image_path')
                        ->label('Ikon Gambar')
                        ->directory('about/contact/icons')
                        ->disk('public')
                        ->image()
                        ->maxSize(5120)
                        ->imageResizeTargetWidth(96)
                        ->imageResizeTargetHeight(96)
                        ->helperText('Upload ikon PNG/SVG/JPG maks. 5 MB. Disarankan ukuran 96x96 px.')
                        ->previewable()
                        ->downloadable()
                        ->openable(),
                    Forms\Components\Textarea::make('lines')
                        ->label('Isi (pisahkan baris dengan enter)')
                        ->rows(4)
                        ->afterStateHydrated(fn (Forms\Components\Textarea $component, $state) => $component->state(is_array($state) ? implode(PHP_EOL, $state) : $state))
                        ->dehydrateStateUsing(function (?string $state) {
                            if (blank($state)) {
                                return [];
                            }

                            return collect(preg_split("/\r\n|\r|\n/", $state))
                                ->filter(fn (?string $line) => filled($line))
                                ->values()
                                ->all();
                        }),
                    Forms\Components\Textarea::make('copy_text')
                        ->label('Teks Tombol Salin')
                        ->rows(3),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(fn () => (AboutContactDetail::max('sort_order') ?? 0) + 1),
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
                    Forms\Components\TextInput::make('icon')
                        ->label('Ikon')
                        ->maxLength(255)
                        ->helperText('Gunakan kelas Font Awesome. Kosongkan jika menggunakan ikon gambar.'),
                    Forms\Components\FileUpload::make('icon_image_path')
                        ->label('Ikon Gambar')
                        ->directory('about/contact/icons')
                        ->disk('public')
                        ->image()
                        ->maxSize(5120)
                        ->imageResizeTargetWidth(96)
                        ->imageResizeTargetHeight(96)
                        ->helperText('Upload ikon PNG/SVG/JPG maks. 5 MB. Disarankan ukuran 96x96 px.')
                        ->previewable()
                        ->downloadable()
                        ->openable(),
                    Forms\Components\Textarea::make('lines')
                        ->label('Isi (pisahkan baris dengan enter)')
                        ->rows(4)
                        ->afterStateHydrated(fn (Forms\Components\Textarea $component, $state) => $component->state(is_array($state) ? implode(PHP_EOL, $state) : $state))
                        ->dehydrateStateUsing(function (?string $state) {
                            if (blank($state)) {
                                return [];
                            }

                            return collect(preg_split("/\r\n|\r|\n/", $state))
                                ->filter(fn (?string $line) => filled($line))
                                ->values()
                                ->all();
                        }),
                    Forms\Components\Textarea::make('copy_text')
                        ->label('Teks Tombol Salin')
                        ->rows(3),
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
