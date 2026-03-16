<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\ExpenseCategory;

class ExpenseCategoryPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function view(Admin $admin, ExpenseCategory $expenseCategory): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return true;
    }

    public function update(Admin $admin, ExpenseCategory $expenseCategory): bool
    {
        return true;
    }

    public function delete(Admin $admin, ExpenseCategory $expenseCategory): bool
    {
        return true;
    }
}
