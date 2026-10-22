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
        // Cek dulu, jika kolom 'logo' belum ada, baru eksekusi.
        // Jika sudah ada, lewati saja agar tidak error.
        if (!Schema::hasColumn('settings', 'logo')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('logo')->nullable(); 
                $table->string('landing_image')->nullable(); 
                $table->string('font_family')->default('Plus Jakarta Sans'); 
            });
        }
    }

public function down(): void
{
    Schema::table('settings', function (Blueprint $table) {
        $table->dropColumn(['logo', 'landing_image', 'font_family']);
    });
}
};
