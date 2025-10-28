<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSettingResource\Pages;
use App\Models\HomePageSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomePageSettingResource extends Resource
{
    protected static ?string $model = HomePageSetting::class;

    protected static ?string $modelLabel = 'Pengaturan Beranda';

    protected static ?string $pluralModelLabel = 'Pengaturan Beranda';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Pengaturan Konten';

    protected static ?string $navigationGroup = 'Konten Beranda';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hero Section')
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('hero_subtitle_primary')
                            ->label('Subjudul Utama')
                            ->maxLength(255),
                        TextInput::make('hero_subtitle_secondary')
                            ->label('Subjudul Tambahan')
                            ->maxLength(255),
                        FileUpload::make('hero_background_image_path')
                            ->label('Gambar Latar Hero')
                            ->directory('home/hero')
                            ->disk('public')
                            ->image()
                            ->maxSize(4096)
                            ->helperText('Disarankan rasio 16:9 dengan ukuran minimal 1920x1080 px.')
                            ->downloadable()
                            ->openable()
                            ->previewable(),
                    ])
                    ->columns(2),
                Section::make('Tentang Kami')
                    ->schema([
                        TextInput::make('about_title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('about_description')
                            ->label('Deskripsi')
                            ->rows(6)
                            ->required(),
                        FileUpload::make('about_image_path')
                            ->label('Gambar Tentang Kami')
                            ->directory('home/about')
                            ->disk('public')
                            ->image()
                            ->maxSize(4096)
                            ->helperText('Disarankan rasio 3:2 dengan ukuran minimal 1200x800 px.')
                            ->downloadable()
                            ->openable()
                            ->previewable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hero_title')
                    ->label('Judul Hero')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('hero_subtitle_primary')
                    ->label('Subjudul Utama')
                    ->limit(45)
                    ->toggleable(),
                TextColumn::make('about_title')
                    ->label('Judul Tentang Kami')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn (HomePageSetting $record) => $record->updated_at?->diffForHumans()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return ! static::getModel()::query()->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomePageSettings::route('/'),
            'create' => Pages\CreateHomePageSetting::route('/create'),
            'edit' => Pages\EditHomePageSetting::route('/{record}/edit'),
        ];
    }
}
