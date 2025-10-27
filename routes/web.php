<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactMessageController;
use App\Models\Article;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

// Route untuk halaman utama
Route::get('/', function () {
    $latestArticles = collect();

    try {
        $latestArticles = Article::published()
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(function (Article $article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt ?? Str::limit(strip_tags($article->body), 140),
                    'url' => route('articles.show', $article->slug),
                    'cover_image_url' => $article->cover_image_url,
                    'published_at' => optional($article->published_at)->format('d/m/Y'),
                ];
            });
    } catch (\PDOException $exception) {
        if (! app()->environment('testing')) {
            report($exception);
        }
    }

    return Inertia::render('Home', [
        'articles' => $latestArticles,
        'articlesIndexUrl' => route('articles.index'),
    ]);
});

// Route untuk halaman About Us
Route::get('/about-us', function () {
    return Inertia::render('AboutUs');
});

// Route untuk halaman Contact
Route::get('/contact', function () {
    return Inertia::render('Contact');
});
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

// Route untuk halaman Artikel
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('articles.show');
