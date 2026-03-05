<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_payments', function (Blueprint $table): void {
            $table->decimal('fx_rate_to_pkr', 12, 4)
                ->nullable()
                ->after('currency')
                ->comment('Custom rate: 1 unit of currency = X PKR');

            $table->decimal('amount_in_pkr', 14, 2)
                ->nullable()
                ->after('fx_rate_to_pkr')
                ->comment('Stored PKR equivalent for this payment');
        });
    }

    public function down(): void
    {
        Schema::table('project_payments', function (Blueprint $table): void {
            $table->dropColumn(['fx_rate_to_pkr', 'amount_in_pkr']);
        });
    }
};

