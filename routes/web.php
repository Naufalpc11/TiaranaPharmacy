<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactMessageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route untuk halaman utama
Route::get('/', function () {
    return Inertia::render('Home');
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

