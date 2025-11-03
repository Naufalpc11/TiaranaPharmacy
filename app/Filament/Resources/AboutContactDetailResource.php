<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutContactDetailResource\Pages;
use App\Models\AboutContactDetail;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AboutContactDetailResource extends Resource
{
    protected static ?string $model = AboutContactDetail::class;

    protected static ?string $modelLabel = 'Informasi Kontak';

    protected static ?string $pluralModelLabel = 'Informasi Kontak';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?string $navigationLabel = 'Informasi Kontak';

    protected static ?int $navigationSort = 30;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                TextInput::make('icon')
                    ->label('Ikon')
                    ->maxLength(255)
                    ->helperText('Gunakan kelas Font Awesome, misal: fa-solid fa-phone. Kosongkan jika memakai ikon gambar.'),
                FileUpload::make('icon_image_path')
                    ->label('Ikon Gambar')
                    ->directory('about/contact/icons')
                    ->disk('public')
                    ->image()
                    ->maxSize(5120)
                    ->imageEditor()
                    ->imageResizeTargetWidth(96)
                    ->imageResizeTargetHeight(96)
                    ->helperText('Upload ikon PNG/SVG/JPG maks. 5 MB. Disarankan ukuran 96x96 px agar tampilan tetap tajam. Gambar akan disesuaikan otomatis bila terlalu besar.')
                    ->downloadable()
                    ->openable()
                    ->previewable(),
                Textarea::make('lines')
                    ->label('Isi (pisahkan baris dengan enter)')
                    ->rows(4)
                    ->helperText('Setiap baris akan tampil sebagai baris baru.')
                    ->afterStateHydrated(function (Textarea $component, $state) {
                        if (is_array($state)) {
                            $component->state(implode(PHP_EOL, array_filter($state)));
                        }
                    })
                    ->dehydrateStateUsing(function (?string $state) {
                        if (blank($state)) {
                            return [];
                        }

                        return collect(preg_split("/\r\n|\r|\n/", $state))
                            ->filter(fn (?string $line) => filled($line))
                            ->values()
                            ->all();
                    }),
                Textarea::make('copy_text')
                    ->label('Teks untuk Tombol Salin')
                    ->rows(3)
                    ->helperText('Biarkan kosong untuk menonaktifkan tombol salin.'),
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
                TextColumn::make('title')
                    ->label('Judul')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->limit(40),
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
            'index' => Pages\ListAboutContactDetails::route('/'),
            'create' => Pages\CreateAboutContactDetail::route('/create'),
            'edit' => Pages\EditAboutContactDetail::route('/{record}/edit'),
        ];
    }
}
