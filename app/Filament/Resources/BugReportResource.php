<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BugReportResource\Pages;
use App\Models\BugReport;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class BugReportResource extends Resource
{
    protected static ?string $model = BugReport::class;

    protected static ?string $modelLabel = 'Laporan Bug';

    protected static ?string $pluralModelLabel = 'Laporan Bug';

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?string $navigationLabel = 'Laporan Bug';

    protected static ?string $navigationGroup = 'Interaksi Pengguna';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('Subjek')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email telah disalin')
                    ->copyMessageDuration(1500)
                    ->placeholder('-'),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('screenshot_original_name')
                    ->label('Lampiran')
                    ->formatStateUsing(fn (?string $state): string => $state ?: 'Tidak ada')
                    ->url(
                        fn (BugReport $record): ?string => $record->screenshot_path
                            ? Storage::disk('public')->url($record->screenshot_path)
                            : null,
                        true
                    )
                    ->toggleable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): string => BugReport::statusLabels()[$state] ?? ($state ?? ''))
                    ->colors([
                        'danger' => fn (?string $state): bool => $state === BugReport::STATUS_NEW,
                        'warning' => fn (?string $state): bool => $state === BugReport::STATUS_IN_PROGRESS,
                        'success' => fn (?string $state): bool => $state === BugReport::STATUS_DONE,
                    ])
                    ->sortable(),
                TextColumn::make('resolved_at')
                    ->label('Diselesaikan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn (BugReport $record) => $record->created_at?->diffForHumans())
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BugReport::statusLabels()),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Laporan Bug')
                    ->modalWidth('3xl')
                    ->form(self::viewActionForm()),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Laporan Bug?')
                    ->modalDescription('Tindakan ini akan menghapus laporan secara permanen beserta lampirannya.')
                    ->successNotificationTitle('Laporan bug berhasil dihapus.'),
                Actions\Action::make('updateStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->modalHeading('Perbarui Status Laporan')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options(BugReport::statusLabels())
                            ->required(),
                    ])
                    ->fillForm(fn (BugReport $record): array => [
                        'status' => $record->status ?? BugReport::STATUS_NEW,
                    ])
                    ->action(function (BugReport $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('markAsNew')
                        ->label('Tandai Baru')
                        ->icon('heroicon-m-sparkles')
                        ->color('primary')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => BugReport::STATUS_NEW]);
                        }),
                    Actions\BulkAction::make('markAsProgress')
                        ->label('Tandai Proses')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => BugReport::STATUS_IN_PROGRESS]);
                        }),
                    Actions\BulkAction::make('markAsDone')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => BugReport::STATUS_DONE]);
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Laporan Terpilih?')
                        ->modalDescription('Laporan yang dipilih akan dihapus permanen beserta lampirannya.'),
                ]),
            ])
            ->emptyStateHeading('Belum ada laporan bug')
            ->emptyStateDescription('Laporan bug dari pengguna akan muncul di sini setelah mereka mengirim formulir.')
            ->poll('30s');
    }

    protected static function viewActionForm(): array
    {
        return [
            Section::make('Detail Laporan')
                ->schema([
                    Placeholder::make('subject')
                        ->label('Subjek')
                        ->content(fn (BugReport $record): string => $record->subject),
                    Placeholder::make('email')
                        ->label('Email')
                        ->content(fn (BugReport $record): string => $record->email ?: '-'),
                    Placeholder::make('status')
                        ->label('Status')
                        ->content(fn (BugReport $record): string => BugReport::statusLabels()[$record->status] ?? 'Baru'),
                    Placeholder::make('created_at')
                        ->label('Dibuat')
                        ->content(fn (BugReport $record): string => $record->created_at?->format('d M Y H:i') ?? '-'),
                    Placeholder::make('resolved_at')
                        ->label('Diselesaikan')
                        ->content(fn (BugReport $record): string => $record->resolved_at?->format('d M Y H:i') ?? '-'),
                ])
                ->columns(2),
            Section::make('Deskripsi')
                ->schema([
                    Textarea::make('description')
                        ->label('Deskripsi Bug')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(10)
                        ->formatStateUsing(fn (BugReport $record): string => $record->description),
                ]),
            Section::make('Lampiran')
                ->schema([
                    Forms\Components\View::make('filament.components.bug-report-screenshot')
                        ->viewData(fn (BugReport $record): array => [
                            'url' => $record->screenshot_path
                                ? Storage::disk('public')->url($record->screenshot_path)
                                : null,
                            'name' => $record->screenshot_original_name,
                        ])
                        ->columnSpanFull(),
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
            'index' => Pages\ListBugReports::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('status', '!=', BugReport::STATUS_DONE)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah laporan bug yang membutuhkan peninjauan';
    }
}
