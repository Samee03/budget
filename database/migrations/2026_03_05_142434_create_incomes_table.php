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
        Schema::create('incomes', function (Blueprint $table): void {
            $table->id();
            $table->date('received_at');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('PKR');
            $table->decimal('fx_rate_to_pkr', 12, 4)->nullable()->comment('1 unit of currency = X PKR');
            $table->decimal('amount_in_pkr', 14, 2)->nullable()->comment('Stored PKR equivalent');
            $table->string('type')->default('other'); // e.g. salary, freelance, refund, gift, other
            $table->string('source')->nullable(); // e.g. employer or client name
            $table->string('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['received_at', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
