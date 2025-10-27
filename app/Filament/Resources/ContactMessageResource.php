<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $modelLabel = 'Pesan Kontak';

    protected static ?string $pluralModelLabel = 'Pesan Kontak';

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationLabel = 'Pesan Kontak';

    protected static ?string $navigationGroup = 'Interaksi Pengguna';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_reviewed')
                            ->label('Sudah dibaca')
                            ->inline(false),
                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('Ditandai pada')
                            ->seconds(false),
                    ])
                    ->columns(2),
                Section::make('Pesan')
                    ->schema([
                        Textarea::make('message')
                            ->label('Pesan')
                            ->rows(8)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email telah disalin')
                    ->copyMessageDuration(1500),
                TextColumn::make('subject')
                    ->label('Subjek')
                    ->toggleable()
                    ->wrap()
                    ->limit(40)
                    ->placeholder('-'),
                TextColumn::make('message')
                    ->label('Pesan')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(),
                ToggleColumn::make('is_reviewed')
                    ->label('Sudah dibaca')
                    ->sortable()
                    ->onIcon('heroicon-m-check-circle')
                    ->offIcon('heroicon-m-bell-alert'),
                TextColumn::make('reviewed_at')
                    ->label('Ditandai pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn (ContactMessage $record) => $record->created_at?->diffForHumans())
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_reviewed')
                    ->label('Status Dibaca')
                    ->trueLabel('Sudah dibaca')
                    ->falseLabel('Belum dibaca')
                    ->placeholder('Semua'),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Pesan Kontak')
                    ->modalWidth('lg')
                    ->form(self::viewActionForm()),
                Actions\Action::make('markAsReviewed')
                    ->label('Tandai Sudah Dibaca')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (ContactMessage $record): bool => ! $record->is_reviewed)
                    ->requiresConfirmation()
                    ->action(function (ContactMessage $record): void {
                        $record->update(['is_reviewed' => true]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('markAsReviewed')
                        ->label('Tandai Sudah Dibaca')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_reviewed' => true]);
                        }),
                    Actions\BulkAction::make('markAsUnread')
                        ->label('Tandai Belum Dibaca')
                        ->icon('heroicon-m-arrow-uturn-left')
                        ->color('gray')
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_reviewed' => false]);
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum ada pesan')
            ->emptyStateDescription('Pesan dari pengunjung akan muncul di sini setelah mereka mengirim formulir kontak.')
            ->poll('30s');
    }

    /**
     * Read-only view action schema.
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected static function viewActionForm(): array
    {
        return [
            Section::make('Informasi Kontak')
                ->schema([
                    Placeholder::make('name')
                        ->label('Nama')
                        ->content(fn (ContactMessage $record): string => $record->name),
                    Placeholder::make('email')
                        ->label('Email')
                        ->content(fn (ContactMessage $record): string => $record->email),
                    Placeholder::make('subject')
                        ->label('Subjek')
                        ->content(fn (ContactMessage $record): string => $record->subject ?: '-'),
                    Placeholder::make('created_at')
                        ->label('Dikirim Pada')
                        ->content(fn (ContactMessage $record): string => $record->created_at?->format('d M Y H:i') ?? '-'),
                    Placeholder::make('is_reviewed')
                        ->label('Status')
                        ->content(fn (ContactMessage $record): string => $record->is_reviewed ? 'Sudah dibaca' : 'Belum dibaca'),
                    Placeholder::make('reviewed_at')
                        ->label('Ditandai Pada')
                        ->content(fn (ContactMessage $record): string => $record->reviewed_at?->format('d M Y H:i') ?? '-'),
                ])
                ->columns(2),
            Section::make('Isi Pesan')
                ->schema([
                    Textarea::make('message')
                        ->label('Pesan')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(10)
                        ->formatStateUsing(fn (ContactMessage $record): string => $record->message),
                ]),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('is_reviewed', false)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah pesan belum dibaca';
    }
}
