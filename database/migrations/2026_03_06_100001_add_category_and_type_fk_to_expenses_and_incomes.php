<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table): void {
            $table->foreignId('expense_category_id')
                ->nullable()
                ->after('project_id')
                ->constrained('expense_categories')
                ->nullOnDelete();
        });

        Schema::table('incomes', function (Blueprint $table): void {
            $table->foreignId('income_type_id')
                ->nullable()
                ->after('account_id')
                ->constrained('income_types')
                ->nullOnDelete();
        });

        $this->backfillExpenseCategories();
        $this->backfillIncomeTypes();

        Schema::table('expenses', function (Blueprint $table): void {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });

        Schema::table('incomes', function (Blueprint $table): void {
            $table->dropIndex(['received_at', 'type']);
            $table->dropColumn('type');
        });

        Schema::table('incomes', function (Blueprint $table): void {
            $table->index(['received_at', 'income_type_id']);
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('project_id');
            $table->index('category');
        });
        Schema::table('incomes', function (Blueprint $table): void {
            $table->string('type')->default('other')->after('account_id');
        });

        $this->restoreExpenseCategories();
        $this->restoreIncomeTypes();

        Schema::table('expenses', function (Blueprint $table): void {
            $table->dropForeign(['expense_category_id']);
            $table->dropColumn('expense_category_id');
        });

        Schema::table('incomes', function (Blueprint $table): void {
            $table->dropIndex(['received_at', 'income_type_id']);
            $table->dropForeign(['income_type_id']);
            $table->dropColumn('income_type_id');
            $table->index(['received_at', 'type']);
        });
    }

    private function backfillExpenseCategories(): void
    {
        $map = DB::table('expense_categories')->pluck('id', 'slug')->all();
        foreach ($map as $slug => $id) {
            DB::table('expenses')->where('category', $slug)->update(['expense_category_id' => $id]);
        }
    }

    private function backfillIncomeTypes(): void
    {
        $map = DB::table('income_types')->pluck('id', 'slug')->all();
        foreach ($map as $slug => $id) {
            DB::table('incomes')->where('type', $slug)->update(['income_type_id' => $id]);
        }
    }

    private function restoreExpenseCategories(): void
    {
        $map = DB::table('expense_categories')->pluck('slug', 'id')->all();
        foreach (DB::table('expenses')->whereNotNull('expense_category_id')->get() as $row) {
            $slug = $map[$row->expense_category_id] ?? null;
            if ($slug) {
                DB::table('expenses')->where('id', $row->id)->update(['category' => $slug]);
            }
        }
    }

    private function restoreIncomeTypes(): void
    {
        $map = DB::table('income_types')->pluck('slug', 'id')->all();
        foreach (DB::table('incomes')->whereNotNull('income_type_id')->get() as $row) {
            $slug = $map[$row->income_type_id] ?? null;
            if ($slug) {
                DB::table('incomes')->where('id', $row->id)->update(['type' => $slug]);
            }
        }
    }
};
