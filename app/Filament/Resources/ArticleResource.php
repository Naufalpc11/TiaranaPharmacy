<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                if ($operation !== 'create' || blank($state)) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->id('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Digunakan pada URL, contoh: /artikel/slug-anda'),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Gambar Sampul')
                            ->image()
                            ->maxSize(10240)
                            ->disk('public')
                            ->directory('articles/covers')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->fetchFileInformation(false)
                            ->getUploadedFileUsing(function ($component, string $file, string | array | null $storedFileNames): ?array {
                                $storage = Storage::disk($component->getDiskName() ?? 'public');

                                if (! $storage->exists($file)) {
                                    return null;
                                }

                                $name = $component->isMultiple()
                                    ? ($storedFileNames[$file] ?? basename($file))
                                    : ($storedFileNames ?? basename($file));

                                return [
                                    'name' => $name,
                                    'size' => max(1, (int) $storage->size($file)),
                                    'type' => $storage->mimeType($file) ?? 'image/jpeg',
                                    'url' => ArticleResource::relativeStorageUrl($storage->url($file)),
                                ];
                            })
                            ->rules(['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'])
                            ->helperText('Format jpg/png/webp, maks 10 MB. Direkomendasikan rasio 4:3.'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now())
                            ->seconds(false),
                    ]),
                Forms\Components\Section::make('Konten')
                    ->schema([
                        Forms\Components\RichEditor::make('body')
                            ->label('Isi Artikel')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'h2',
                                'h3',
                                'codeBlock',
                                'undo',
                                'redo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('articles/attachments')
                            ->columnSpanFull()
                            ->required(),
                    ]),
                Forms\Components\Section::make('SEO')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(320),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Sampul')
                    ->disk('public')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable()
                    ->searchable()
                    ->copyable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Terbit')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah terbit')
                    ->falseLabel('Draft')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now()),
                        false: fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '>', now()),
                    ),
            ])
            ->defaultSort('published_at', 'desc')
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
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    protected static function relativeStorageUrl(string $url): string
    {
        $appUrl = rtrim(config('app.url') ?? '', '/');

        if ($appUrl && Str::startsWith($url, $appUrl)) {
            return Str::after($url, $appUrl) ?: '/';
        }

        return $url;
    }
}
