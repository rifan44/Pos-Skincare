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
        // 1. Tambah SoftDeletes ke Kategori
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 2. Tambah Tanggal Expired & SoftDeletes ke Produk Skincare
        Schema::table('products', function (Blueprint $table) {
            $table->date('expired_date')->nullable()->after('stock');
            $table->softDeletes();
        });

        // 3. Tambah kolom HPP ke Detail Transaksi (SANGAT KRUSIAL UNTUK AKUNTANSI)
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->integer('hpp')->default(0)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('expired_date');
            $table->dropSoftDeletes();
        });
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });
    }
};
