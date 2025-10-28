<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerLogoResource\Pages;
use App\Models\PartnerLogo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerLogoResource extends Resource
{
    protected static ?string $model = PartnerLogo::class;

    protected static ?string $modelLabel = 'Logo Partner';

    protected static ?string $pluralModelLabel = 'Logo Partner';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Konten Beranda';

    protected static ?string $navigationLabel = 'Partner PBF';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Partner')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(1)
                            ->minValue(0),
                        FileUpload::make('image_path')
                            ->label('Logo')
                            ->directory('home/partners')
                            ->disk('public')
                            ->image()
                            ->maxSize(4096)
                            ->helperText('Disarankan ukuran 220x96 px (rasio 2.3:1) dengan latar transparan.')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Logo')
                    ->disk('public')
                    ->width(80)
                    ->height(40)
                    ->circular(false)
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable()
                    ->limit(40),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
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
            'index' => Pages\ListPartnerLogos::route('/'),
            'create' => Pages\CreatePartnerLogo::route('/create'),
            'edit' => Pages\EditPartnerLogo::route('/{record}/edit'),
        ];
    }
}
