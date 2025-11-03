<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutHistoryStatResource\Pages;
use App\Models\AboutHistoryStat;
use App\Models\AboutPageSetting;
use App\Services\AboutPageContentService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AboutHistoryStatResource extends Resource
{
    protected static ?string $model = AboutHistoryStat::class;

    protected static ?string $modelLabel = 'Statistik Sejarah';

    protected static ?string $pluralModelLabel = 'Statistik Sejarah';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?string $navigationLabel = 'Statistik Sejarah';

    protected static ?int $navigationSort = 20;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('icon')
                    ->label('Ikon')
                    ->maxLength(255)
                    ->helperText('Gunakan kelas Font Awesome, misal: fa-solid fa-pills. Kosongkan jika memakai ikon gambar.'),
                FileUpload::make('icon_image_path')
                    ->label('Ikon Gambar')
                    ->directory('about/history/icons')
                    ->disk('public')
                    ->image()
                    ->maxSize(5120)
                    ->imageEditor()
                    ->imageResizeTargetWidth(128)
                    ->imageResizeTargetHeight(128)
                    ->helperText('Upload ikon PNG/SVG/JPG maks. 5 MB. Disarankan ukuran 128x128 px agar tampil optimal. Gambar akan disesuaikan otomatis bila terlalu besar.')
                    ->downloadable()
                    ->openable()
                    ->previewable(),
                TextInput::make('value')
                    ->label('Nilai')
                    ->required()
                    ->maxLength(255),
                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->limit(40),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('editHistoryContent')
                    ->label('Edit Konten Sejarah')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading('Edit Konten Sejarah')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalWidth('lg')
                    ->form([
                        TextInput::make('history_title')
                            ->label('Judul')
                            ->maxLength(255),
                        Textarea::make('history_description')
                            ->label('Deskripsi')
                            ->rows(6)
                            ->helperText('Gunakan enter untuk memisahkan paragraf.'),
                        FileUpload::make('history_image_path')
                            ->label('Gambar Sejarah')
                            ->directory('about/history')
                            ->disk('public')
                            ->image()
                            ->maxSize(4096)
                            ->imageEditor()
                            ->imageResizeTargetWidth(1200)
                            ->imageResizeTargetHeight(800)
                            ->helperText('Disarankan rasio 3:2 dengan ukuran minimal 1200x800 px. Gambar akan disesuaikan otomatis bila terlalu besar.')
                            ->downloadable()
                            ->openable()
                            ->previewable(),
                    ])
                    ->mountUsing(function (Action $action) {
                        $settings = AboutPageSetting::query()->first();
                        $history = app(AboutPageContentService::class)->get()['history'] ?? [];

                        $action->fillForm([
                            'history_title' => $settings?->history_title ?? ($history['title'] ?? null),
                            'history_description' => $settings?->history_description ?? ($history['description'] ?? null),
                            'history_image_path' => $settings?->history_image_path,
                        ]);
                    })
                    ->action(function (array $data): void {
                        $settings = AboutPageSetting::query()->first();

                        if (! $settings) {
                            $settings = AboutPageSetting::query()->create([]);
                        }

                        $settings->fill([
                            'history_title' => $data['history_title'] ?? $settings->history_title,
                            'history_description' => $data['history_description'] ?? $settings->history_description,
                        ]);

                        if (array_key_exists('history_image_path', $data)) {
                            $settings->history_image_path = $data['history_image_path'] ?? null;
                        }

                        $settings->save();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAboutHistoryStats::route('/'),
            'create' => Pages\CreateAboutHistoryStat::route('/create'),
            'edit' => Pages\EditAboutHistoryStat::route('/{record}/edit'),
        ];
    }
}
