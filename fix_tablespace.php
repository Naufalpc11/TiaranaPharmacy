<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Fixing tablespace issues...\n\n";
    
    // Get all .ibd files that might be orphaned
    $tables = [
        'migrations', 'users', 'sessions', 'cache', 'cache_locks',
        'password_reset_tokens', 'articles', 'contact_messages',
        'home_page_settings', 'home_services', 'home_about_features',
        'home_feature_highlights', 'about_page_settings', 'about_missions',
        'about_history_stats', 'about_contact_details', 'bug_reports',
        'chat_conversations', 'chat_messages', 'footer_settings',
        'medication_assets', 'partner_logos', 'jobs', 'job_batches', 'failed_jobs'
    ];
    
    foreach ($tables as $table) {
        echo "Processing table: $table\n";
        try {
            // Try to discard tablespace
            DB::statement("ALTER TABLE `$table` DISCARD TABLESPACE");
            echo "  ✓ Discarded tablespace for $table\n";
        } catch (\Exception $e) {
            // Table might not exist, that's okay
            echo "  - Skip (table doesn't exist or already clean)\n";
        }
    }
    
    echo "\nDone!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
