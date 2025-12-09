<?php

use Illuminate\Support\Facades\Route;
use App\Services\SupabaseService;

Route::get('/test-supabase', function() {
    $supabase = app(SupabaseService::class);
    $result = $supabase->sendPasswordResetEmail('tiaranafarma@gmail.com');
    return response()->json($result);
});
