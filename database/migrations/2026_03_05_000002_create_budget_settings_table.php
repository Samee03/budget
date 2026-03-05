<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_settings', function (Blueprint $table): void {
            $table->id();
            $table->decimal('usd_to_pkr_rate', 12, 4)->nullable()->comment('1 USD = X PKR');
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->string('opening_balance_currency', 3)->default('PKR');
            $table->date('opening_balance_as_of_date')->nullable();
            $table->timestamps();
        });

        // Single row: insert default
        DB::table('budget_settings')->insert([
            'usd_to_pkr_rate' => 278.0,
            'opening_balance' => 0,
            'opening_balance_currency' => 'PKR',
            'opening_balance_as_of_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_settings');
    }
};
