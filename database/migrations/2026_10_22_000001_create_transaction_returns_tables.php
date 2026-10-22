<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaction_returns')) {
            Schema::create('transaction_returns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_id')->constrained()->restrictOnDelete();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('processed_by_name');
                $table->text('reason');
                $table->integer('total_amount');
                $table->string('refund_method', 20)->default('cash');
                $table->string('reference')->nullable();
                $table->timestamps();
                $table->index(['transaction_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('transaction_return_details')) {
            Schema::create('transaction_return_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_return_id')->constrained('transaction_returns')->restrictOnDelete();
                $table->foreignId('transaction_detail_id')->constrained('transaction_details')->restrictOnDelete();
                $table->integer('quantity');
                $table->integer('restocked_quantity')->default(0);
                $table->integer('unit_price_snapshot');
                $table->integer('hpp_snapshot')->default(0);
                $table->integer('refund_amount');
                $table->timestamps();
                $table->unique(['transaction_return_id', 'transaction_detail_id'], 'trd_return_line_unique');
            });
        }

        if (! Schema::hasIndex('transaction_return_details', 'trd_return_line_unique')) {
            Schema::table('transaction_return_details', function (Blueprint $table) {
                $table->unique(['transaction_return_id', 'transaction_detail_id'], 'trd_return_line_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_return_details');
        Schema::dropIfExists('transaction_returns');
    }
};
