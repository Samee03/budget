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
        Schema::table('incomes', function (Blueprint $table): void {
            $table->foreignId('account_id')
                ->nullable()
                ->after('id')
                ->constrained('accounts')
                ->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table): void {
            $table->foreignId('account_id')
                ->nullable()
                ->after('project_id')
                ->constrained('accounts')
                ->nullOnDelete();
        });

        Schema::table('project_payments', function (Blueprint $table): void {
            $table->foreignId('account_id')
                ->nullable()
                ->after('project_id')
                ->constrained('accounts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('account_id');
        });

        Schema::table('expenses', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('account_id');
        });

        Schema::table('project_payments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('account_id');
        });
    }
};
