<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicationAssetResource\Pages;
use App\Models\MedicationAsset;
use App\Support\AgeRange;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicationAssetResource extends Resource
{
    protected static ?string $model = MedicationAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'AI Dataset';

    protected static ?string $navigationLabel = 'Dataset Obat';

    protected static ?string $pluralModelLabel = 'Dataset Obat';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dataset')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Obat')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('function_label')
                            ->label('Fungsi / Keluhan')
                            ->helperText('Contoh: Penurun demam, obat batuk kering, dsb.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('form')
                            ->label('Bentuk Obat')
                            ->placeholder('Sirup, tablet, kapsul, tetes, dsb.')
                            ->maxLength(255),
                        Select::make('age_label')
                            ->label('Rentang Usia')
                            ->options(AgeRange::options())
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Catatan Admin (opsional)')
                            ->rows(3)
                            ->maxLength(2000),
                    ])
                    ->columns(2),
                Section::make('Gambar Dataset')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Gambar Obat')
                            ->disk('public')
                            ->directory('dataset/medications')
                            ->visibility('public')
                            ->image()
                            ->maxSize(5120)
                            ->required()
                            ->downloadable()
                            ->openable()
                            ->previewable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(80)
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('function_label')
                    ->label('Fungsi')
                    ->toggleable()
                    ->limit(40),
                TextColumn::make('age_label')
                    ->label('Rentang Usia')
                    ->formatStateUsing(fn (string $state) => AgeRange::label($state))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('updated_at', 'desc')
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
            'index' => Pages\ListMedicationAssets::route('/'),
            'create' => Pages\CreateMedicationAsset::route('/create'),
            'edit' => Pages\EditMedicationAsset::route('/{record}/edit'),
        ];
    }
}
