<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bug_reports')) {
            return;
        }

        Schema::table('bug_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('bug_reports', 'status')) {
                $table->string('status', 20)->default('baru')->after('description');
            }
        });

        DB::table('bug_reports')
            ->where('is_resolved', true)
            ->update(['status' => 'selesai']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('bug_reports')) {
            return;
        }

        Schema::table('bug_reports', function (Blueprint $table) {
            if (Schema::hasColumn('bug_reports', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

