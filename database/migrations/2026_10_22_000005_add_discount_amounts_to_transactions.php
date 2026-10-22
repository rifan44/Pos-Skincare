<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('subtotal_amount')->default(0)->after('total_amount');
            $table->integer('discount_amount')->default(0)->after('subtotal_amount');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->integer('discount_amount')->default(0)->after('price');
        });

        DB::table('transactions')->update([
            'subtotal_amount' => DB::raw('total_amount'),
        ]);
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['subtotal_amount', 'discount_amount']);
        });
    }
};
