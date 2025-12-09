$supabase = app(App\Services\SupabaseService::class);
$result = $supabase->sendPasswordResetEmail('tiaranafarma@gmail.com');
dd($result);
