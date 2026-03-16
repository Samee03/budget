<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('income_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $this->seedExpenseCategories();
        $this->seedIncomeTypes();
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('income_types');
    }

    private function seedExpenseCategories(): void
    {
        $categories = [
            ['name' => 'Groceries', 'slug' => 'groceries'],
            ['name' => 'Online shopping', 'slug' => 'online_shopping'],
            ['name' => 'ATM withdrawal', 'slug' => 'atm_withdrawal'],
            ['name' => 'Outsourcing / Contractors', 'slug' => 'outsourcing'],
            ['name' => 'Tools & Software', 'slug' => 'tools'],
            ['name' => 'Travel', 'slug' => 'travel'],
            ['name' => 'Other', 'slug' => 'other'],
        ];
        foreach ($categories as $cat) {
            DB::table('expense_categories')->insert([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedIncomeTypes(): void
    {
        $types = [
            ['name' => 'Salary', 'slug' => 'salary'],
            ['name' => 'Freelance / Side project', 'slug' => 'freelance'],
            ['name' => 'Refund', 'slug' => 'refund'],
            ['name' => 'Gift', 'slug' => 'gift'],
            ['name' => 'Other', 'slug' => 'other'],
        ];
        foreach ($types as $type) {
            DB::table('income_types')->insert([
                'name' => $type['name'],
                'slug' => $type['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
