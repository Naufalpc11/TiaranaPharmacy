<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route untuk halaman utama
Route::get('/', function () {
    // Fungsi Inertia::render akan mencari komponen Vue di resources/js/Pages/Home.vue
    return Inertia::render('Home', [
        'username' => 'Pengguna Apotik', // Anda bisa melewatkan data ke komponen Vue
        'productsCount' => 42,
    ]);
});
