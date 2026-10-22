<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->string('name');
        $table->string('sku')->nullable(); // Kode barcode barang
        $table->integer('purchase_price'); // Harga modal/HPP
        $table->integer('margin_percent'); // Keuntungan dalam persen (contoh: 20)
        $table->integer('selling_price');  // Harga jual setelah ditambah margin
        $table->integer('stock'); // Sisa stok
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
