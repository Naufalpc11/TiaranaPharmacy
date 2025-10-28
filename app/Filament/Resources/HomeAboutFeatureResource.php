<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeAboutFeatureResource\Pages;
use App\Models\HomeAboutFeature;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomeAboutFeatureResource extends Resource
{
    protected static ?string $model = HomeAboutFeature::class;

    protected static ?string $modelLabel = 'Keunggulan Tentang Kami';

    protected static ?string $pluralModelLabel = 'Keunggulan Tentang Kami';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Konten Beranda';

    protected static ?string $navigationLabel = 'Keunggulan Kami';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Keunggulan')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('icon')
                            ->label('Kelas Ikon')
                            ->helperText('Gunakan kelas Font Awesome, misalnya "fas fa-heart".')
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(1)
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->sortable()
                    ->searchable()
                    ->limit(40),
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->limit(40)
                    ->toggleable(),
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
            'index' => Pages\ListHomeAboutFeatures::route('/'),
            'create' => Pages\CreateHomeAboutFeature::route('/create'),
            'edit' => Pages\EditHomeAboutFeature::route('/{record}/edit'),
        ];
    }
}
