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
        Schema::create('about_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_background_image_path')->nullable();
            $table->string('hero_primary_button_text')->nullable();
            $table->string('hero_primary_button_url')->nullable();
            $table->string('hero_secondary_button_text')->nullable();
            $table->string('hero_secondary_button_url')->nullable();

            $table->string('vision_title')->nullable();
            $table->text('vision_text')->nullable();

            $table->string('mission_title')->nullable();

            $table->string('history_title')->nullable();
            $table->text('history_description')->nullable();
            $table->string('history_image_path')->nullable();

            $table->string('team_title')->nullable();
            $table->text('team_intro')->nullable();

            $table->string('pharmacist_name')->nullable();
            $table->string('pharmacist_role')->nullable();
            $table->string('pharmacist_stra')->nullable();
            $table->string('pharmacist_sipa')->nullable();
            $table->string('pharmacist_schedule')->nullable();
            $table->string('pharmacist_photo_path')->nullable();
            $table->string('pharmacist_photo_alt')->nullable();
            $table->json('pharmacist_badges')->nullable();

            $table->string('location_title')->nullable();
            $table->text('location_intro')->nullable();
            $table->text('location_map_embed_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_page_settings');
    }
};
