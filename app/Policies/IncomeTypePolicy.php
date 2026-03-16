<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\IncomeType;

class IncomeTypePolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function view(Admin $admin, IncomeType $incomeType): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return true;
    }

    public function update(Admin $admin, IncomeType $incomeType): bool
    {
        return true;
    }

    public function delete(Admin $admin, IncomeType $incomeType): bool
    {
        return true;
    }
}
