<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeServiceResource\Pages;
use App\Models\HomeService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomeServiceResource extends Resource
{
    protected static ?string $model = HomeService::class;

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Konten Beranda';

    protected static ?string $navigationLabel = 'Layanan Kami';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Layanan')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('icon')
                            ->label('Kelas Ikon')
                            ->helperText('Gunakan kelas Font Awesome, misalnya "fas fa-prescription-bottle-alt" (opsional).')
                            ->maxLength(255)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                            ->rule('required_without:icon_image_path'),
                        FileUpload::make('icon_image_path')
                            ->label('Ikon Gambar')
                            ->directory('home/services/icons')
                            ->disk('public')
                            ->image()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                            ->helperText('Opsional. Upload ikon PNG transparan berukuran 80x80 px.')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->rule('required_without:icon')
                            ->columnSpan(2),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->required()
                            ->columnSpan(2),
                        TagsInput::make('items')
                            ->label('Poin Layanan')
                            ->placeholder('Tekan Enter untuk menambah poin')
                            ->required()
                            ->hint('Poin ini akan ditampilkan sebagai daftar cek.')
                            ->columnSpan(2),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(1)
                            ->minValue(0),
                    ])
                    ->columns(2),
                Section::make('Gambar Layanan')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Gambar')
                            ->directory('home/services')
                            ->disk('public')
                            ->image()
                            ->maxSize(4096)
                            ->helperText('Disarankan rasio landscape dengan ukuran minimal 1200x800 px.')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->sortable()
                    ->searchable()
                    ->limit(40),
                ImageColumn::make('icon_image_path')
                    ->label('Ikon Gambar')
                    ->disk('public')
                    ->width(40)
                    ->height(40)
                    ->toggleable(),
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->formatStateUsing(fn (?string $state) => $state ?: '--')
                    ->limit(40)
                    ->toggleable(),
                BadgeColumn::make('items')
                    ->label('Jumlah Poin')
                    ->colors(['primary'])
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) . ' poin' : '0 poin'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeServices::route('/'),
            'create' => Pages\CreateHomeService::route('/create'),
            'edit' => Pages\EditHomeService::route('/{record}/edit'),
        ];
    }
}
