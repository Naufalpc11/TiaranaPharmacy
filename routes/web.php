<?php

use App\Http\Controllers\Api\GeminiChatController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\MedicationImageController;
use App\Models\Article;
use App\Services\AboutPageContentService;
use App\Services\HomePageContentService;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

// Route untuk halaman utama
Route::get('/', function (HomePageContentService $homePageContentService) {
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
        'homeContent' => $homePageContentService->get(),
    ]);
});

// Route untuk halaman About Us
Route::get('/about-us', function (AboutPageContentService $aboutPageContentService) {
    return Inertia::render('AboutUs', [
        'aboutContent' => $aboutPageContentService->get(),
    ]);
});

// Route untuk halaman Contact
Route::get('/contact', function () {
    return Inertia::render('Contact');
});
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

// Route untuk halaman Report Bug
Route::get('/report-bug', function () {
    return Inertia::render('ReportBug');
})->name('bug-report.create');
Route::post('/report-bug', [BugReportController::class, 'store'])->name('bug-report.store');

// Route untuk halaman Chatbot
Route::get('/chatbot', function () {
    return Inertia::render('Chatbot');
})->name('chatbot');

Route::get('/medication-images/{slug}', MedicationImageController::class)
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('medications.dataset.show');

// Route untuk halaman Artikel
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::prefix('api')->group(function () {
    Route::post('chatbot/message', [GeminiChatController::class, 'sendMessage'])
        ->name('api.chatbot.message');
    Route::delete('chatbot/conversations/{conversation}', [GeminiChatController::class, 'destroy'])
        ->name('api.chatbot.conversations.destroy');

    Route::middleware('auth')->group(function () {
        Route::get('chatbot/conversations', [GeminiChatController::class, 'index'])
            ->name('api.chatbot.conversations.index');
        Route::get('chatbot/conversations/{conversation}', [GeminiChatController::class, 'show'])
            ->name('api.chatbot.conversations.show');
    });
});

// Route khusus untuk menampilkan halaman 404 (dapat di-link langsung)
Route::get('/not-found', function () {
    return Inertia::render('NotFound')
        ->toResponse(request())
        ->setStatusCode(404);
})->name('not-found');

// Test Supabase
Route::get('/test-supabase', function(SupabaseService $supabase) {
    $result = $supabase->sendPasswordResetEmail('tiaranafarma@gmail.com');
    return response()->json($result);
})->name('test.supabase');

Route::get('/test-signup', function(SupabaseService $supabase) {
    $result = $supabase->signUp('testuser@example.com', 'Password123!');
    return response()->json($result);
})->name('test.signup');

Route::get('/test-signin', function(SupabaseService $supabase) {
    $result = $supabase->signIn('tiaranafarma@gmail.com', 'TiaranaFarma1774');
    return response()->json($result);
})->name('test.signin');

// Routes untuk forgot password & reset password
Route::view('/auth/forgot-password', 'filament.admin.forgot-password')
    ->name('password.forgot.form');
Route::view('/auth/reset-password', 'filament.admin.reset-password')
    ->name('password.reset.form');
Route::post('/auth/forgot-password', [PasswordResetController::class, 'forgot'])
    ->name('password.forgot');
Route::post('/auth/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.reset');

Route::fallback(function () {
    return Inertia::render('NotFound')
        ->toResponse(request())
        ->setStatusCode(404);
});
