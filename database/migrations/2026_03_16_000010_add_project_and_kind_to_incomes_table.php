<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table): void {
            $table->foreignId('project_id')
                ->nullable()
                ->after('account_id')
                ->constrained()
                ->nullOnDelete();

            $table->string('income_kind', 32)
                ->default('other')
                ->after('type')
                ->comment('project_payment, salary, other, etc.');

            $table->string('payment_reference')
                ->nullable()
                ->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('project_id');
            $table->dropColumn(['income_kind', 'payment_reference']);
        });
    }
};

