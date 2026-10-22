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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('landing_title')->default('Solusi Cerdas Kelola');
            $table->string('landing_text_highlight')->default('Toko Skincare');
            $table->text('landing_description')->nullable();
            $table->string('primary_color')->default('#ffb6b9'); // Warna default pink
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['landing_title', 'landing_text_highlight', 'landing_description', 'primary_color']);
        });
    }
};
