<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medication_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('function_label');
            $table->string('form')->nullable();
            $table->string('age_label')->nullable()->index();
            $table->unsignedTinyInteger('age_min_years')->nullable();
            $table->unsignedTinyInteger('age_max_years')->nullable();
            $table->string('image_path');
            $table->string('image_disk')->default('public');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_assets');
    }
};
