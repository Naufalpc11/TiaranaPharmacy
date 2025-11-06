<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterSettingResource\Pages;
use App\Models\FooterSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FooterSettingResource extends Resource
{
    protected static ?string $model = FooterSetting::class;

    protected static ?string $modelLabel = 'Pengaturan Footer';

    protected static ?string $pluralModelLabel = 'Pengaturan Footer';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Pengaturan Umum';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profil Footer')
                    ->schema([
                        Textarea::make('tagline')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Kontak')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon')
                            ->maxLength(255),
                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('contact_address')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(2),
                Section::make('Jam Operasional')
                    ->schema([
                        TextInput::make('operational_hours_primary')
                            ->label('Jam Operasional (Hari Kerja)')
                            ->maxLength(255),
                        TextInput::make('operational_hours_secondary')
                            ->label('Jam Operasional (Akhir Pekan)')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Tautan Sosial Media')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('whatsapp_url')
                            ->label('WhatsApp')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tagline')
                    ->label('Deskripsi')
                    ->limit(50),
                TextColumn::make('contact_phone')
                    ->label('Telepon')
                    ->limit(30),
                TextColumn::make('contact_email')
                    ->label('Email')
                    ->limit(30),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn (FooterSetting $record) => $record->updated_at?->diffForHumans()),
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
            'index' => Pages\ListFooterSettings::route('/'),
            'create' => Pages\CreateFooterSetting::route('/create'),
            'edit' => Pages\EditFooterSetting::route('/{record}/edit'),
        ];
    }
}
