<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('cash')->after('customer_type');
            $table->string('customer_name')->nullable()->after('payment_method');
            $table->string('customer_phone', 30)->nullable()->after('customer_name');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn(['payment_method', 'customer_name', 'customer_phone']);
        });
    }
};
