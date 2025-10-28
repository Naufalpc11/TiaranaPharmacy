<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeFeatureHighlightResource\Pages;
use App\Models\HomeFeatureHighlight;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomeFeatureHighlightResource extends Resource
{
    protected static ?string $model = HomeFeatureHighlight::class;

    protected static ?string $modelLabel = 'Highlight Fitur';

    protected static ?string $pluralModelLabel = 'Highlight Fitur';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Konten Beranda';

    protected static ?string $navigationLabel = 'Highlight Fitur';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Highlight')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('icon')
                            ->label('Kelas Ikon')
                            ->helperText('Gunakan kelas Font Awesome, misalnya "fas fa-pills".')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->required(),
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
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('icon')
                    ->label('Ikon')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(80)
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
            'index' => Pages\ListHomeFeatureHighlights::route('/'),
            'create' => Pages\CreateHomeFeatureHighlight::route('/create'),
            'edit' => Pages\EditHomeFeatureHighlight::route('/{record}/edit'),
        ];
    }
}
