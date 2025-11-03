<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutPageSettingResource\Pages;
use App\Models\AboutPageSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AboutPageSettingResource extends Resource
{
    protected static ?string $model = AboutPageSetting::class;

    protected static ?string $modelLabel = 'Pengaturan Tentang Kami';

    protected static ?string $pluralModelLabel = 'Pengaturan Tentang Kami';

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'Pengaturan Tentang Kami';

    protected static ?string $navigationGroup = 'Konten Tentang Kami';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('PengaturanTentangKami')
                    ->tabs([
                        Tab::make('Hero')->schema(self::getHeroSchema()),
                        Tab::make('Visi')->schema(self::getVisionSchema()),
                        Tab::make('Misi')->schema(self::getMissionSchema()),
                        Tab::make('Sejarah')->schema(self::getHistorySchema()),
                        Tab::make('Tim Apoteker')->schema(self::getTeamSchema()),
                        Tab::make('Lokasi')->schema(self::getLocationSchema()),
                    ]),
            ]);
    }

    public static function getHeroSchema(): array
    {
        return [
            TextInput::make('hero_title')
                ->label('Judul')
                ->maxLength(255)
                ->required(),
            Textarea::make('hero_subtitle')
                ->label('Deskripsi Singkat')
                ->rows(3),
            TextInput::make('hero_primary_button_text')
                ->label('Teks Tombol Utama')
                ->maxLength(120),
            TextInput::make('hero_primary_button_url')
                ->label('Tujuan Tombol Utama')
                ->maxLength(255)
                ->helperText('Gunakan tautan atau anchor (#lokasi).'),
            TextInput::make('hero_secondary_button_text')
                ->label('Teks Tombol Sekunder')
                ->maxLength(120),
            TextInput::make('hero_secondary_button_url')
                ->label('Tujuan Tombol Sekunder')
                ->maxLength(255),
            FileUpload::make('hero_background_image_path')
                ->label('Gambar Latar Hero')
                ->directory('about/hero')
                ->disk('public')
                ->image()
                ->maxSize(4096)
                ->helperText('Disarankan ukuran minimal 1920x1080 piksel (rasio 16:9).')
                ->imageResizeTargetWidth(1920)
                ->imageResizeTargetHeight(1080)
                ->downloadable()
                ->openable()
                ->previewable(),
        ];
    }

    public static function getVisionSchema(): array
    {
        return [
            TextInput::make('vision_title')
                ->label('Judul Visi')
                ->maxLength(255),
            Textarea::make('vision_text')
                ->label('Deskripsi Visi')
                ->rows(4)
                ->helperText('Tuliskan visi perusahaan. Gunakan paragraf pendek untuk memudahkan pembacaan.'),
        ];
    }

    public static function getMissionSchema(): array
    {
        return [
            TextInput::make('mission_title')
                ->label('Judul Misi')
                ->maxLength(255)
                ->helperText('Judul ini ditampilkan di atas daftar misi. Daftar misi dapat diatur melalui menu misi.'),
        ];
    }

    public static function getHistorySchema(): array
    {
        return [
            TextInput::make('history_title')
                ->label('Judul')
                ->maxLength(255),
            Textarea::make('history_description')
                ->label('Deskripsi')
                ->rows(6)
                ->helperText('Gunakan enter untuk memisahkan paragraf.'),
            FileUpload::make('history_image_path')
                ->label('Gambar Sejarah')
                ->directory('about/history')
                ->disk('public')
                ->image()
                ->maxSize(4096)
                ->helperText('Disarankan rasio 3:2 dengan ukuran minimal 1200x800 piksel. Gambar akan disesuaikan otomatis bila terlalu besar.')
                ->imageResizeTargetWidth(1200)
                ->imageResizeTargetHeight(800)
                ->downloadable()
                ->openable()
                ->previewable(),
        ];
    }

    public static function getTeamSchema(): array
    {
        return [
            TextInput::make('team_title')
                ->label('Judul Bagian')
                ->maxLength(255),
            Textarea::make('team_intro')
                ->label('Deskripsi Singkat')
                ->rows(3),
            TextInput::make('pharmacist_name')
                ->label('Nama Apoteker')
                ->maxLength(255),
            TextInput::make('pharmacist_role')
                ->label('Peran/Jabatan')
                ->maxLength(255),
            TextInput::make('pharmacist_stra')
                ->label('Nomor STRA')
                ->maxLength(255),
            TextInput::make('pharmacist_sipa')
                ->label('Nomor SIPA')
                ->maxLength(255),
            TextInput::make('pharmacist_schedule')
                ->label('Jadwal')
                ->maxLength(255),
            TagsInput::make('pharmacist_badges')
                ->label('Badge Apoteker')
                ->placeholder('Tambahkan badge baru')
                ->helperText('Contoh: STRA & SIPA terverifikasi'),
            FileUpload::make('pharmacist_photo_path')
                ->label('Foto Apoteker')
                ->directory('about/pharmacist')
                ->disk('public')
                ->image()
                ->maxSize(4096)
                ->helperText('Gunakan foto dengan rasio 1:1 (disarankan 600x600 px). Gambar akan disesuaikan otomatis bila lebih besar.')
                ->imageResizeTargetWidth(600)
                ->imageResizeTargetHeight(600)
                ->downloadable()
                ->openable()
                ->previewable(),
            TextInput::make('pharmacist_photo_alt')
                ->label('Teks Alt Foto')
                ->maxLength(255),
        ];
    }

    public static function getLocationSchema(): array
    {
        return [
            TextInput::make('location_title')
                ->label('Judul Lokasi')
                ->maxLength(255),
            Textarea::make('location_intro')
                ->label('Deskripsi Pendukung')
                ->rows(3),
            Textarea::make('location_map_embed_url')
                ->label('URL Embed Peta')
                ->rows(4)
                ->helperText('Tempelkan nilai atribut src dari Google Maps Embed.'),
        ];
    }

    public static function getNavigationItems(): array
    {
        $record = static::getModel()::query()->first();
        $hasRecord = (bool) $record;

        $routeName = $hasRecord ? 'edit' : 'create';
        $routeParameters = $hasRecord ? ['record' => $record?->getKey()] : [];

        $baseUrl = static::getUrl($routeName, $routeParameters);

        $tabBaseId = 'about-page-settings';

        $items = [
            ['label' => 'Hero', 'icon' => 'heroicon-o-photo', 'slug' => 'hero', 'sort' => 1],
            ['label' => 'Visi', 'icon' => 'heroicon-o-light-bulb', 'slug' => 'visi', 'sort' => 2],
            ['label' => 'Misi', 'icon' => 'heroicon-o-list-bullet', 'slug' => 'misi', 'sort' => 3],
            ['label' => 'Sejarah', 'icon' => 'heroicon-o-book-open', 'slug' => 'sejarah', 'sort' => 4],
            ['label' => 'Tim Apoteker', 'icon' => 'heroicon-o-user-group', 'slug' => 'tim-apoteker', 'sort' => 5],
            ['label' => 'Lokasi', 'icon' => 'heroicon-o-map-pin', 'slug' => 'lokasi', 'sort' => 6],
        ];

        return array_map(function (array $item) use ($baseUrl, $tabBaseId) {
            $querySeparator = str_contains($baseUrl, '?') ? '&' : '?';
            $tabId = "{$tabBaseId}-{$item['slug']}-tab";
            $url = "{$baseUrl}{$querySeparator}section={$tabId}";

            return NavigationItem::make($item['label'])
                ->group(static::$navigationGroup)
                ->icon($item['icon'])
                ->sort($item['sort'])
                ->url($url);
        }, $items);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hero_title')
                    ->label('Judul Hero')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('team_title')
                    ->label('Judul Tim')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('location_title')
                    ->label('Judul Lokasi')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn (AboutPageSetting $record) => $record->updated_at?->diffForHumans()),
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
            'index' => Pages\ListAboutPageSettings::route('/'),
            'create' => Pages\CreateAboutPageSetting::route('/create'),
            'edit' => Pages\EditAboutPageSetting::route('/{record}/edit'),
        ];
    }
}
