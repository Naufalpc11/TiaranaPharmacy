<?php

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
