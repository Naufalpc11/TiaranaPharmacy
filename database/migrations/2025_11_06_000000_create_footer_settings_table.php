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
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tagline')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('operational_hours_primary')->nullable();
            $table->string('operational_hours_secondary')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->timestamps();
        });

        DB::table('footer_settings')->insert([
            'tagline' => 'Melayani dengan sepenuh hati sejak 2010',
            'contact_phone' => '0812-3456-7890',
            'contact_email' => 'tiaranafarma@gmail.com',
            'contact_address' => 'Jl. Sepinggan Baru',
            'operational_hours_primary' => 'Senin - Sabtu: 08:00 - 22:00',
            'operational_hours_secondary' => 'Minggu: 09:00 - 22:00',
            'facebook_url' => '#',
            'instagram_url' => '#',
            'whatsapp_url' => '#',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
