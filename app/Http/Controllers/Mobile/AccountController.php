<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BudgetSetting;
use App\Traits\ApiResponse;

class AccountController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $accounts = Account::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Account $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'type' => $a->type,
                'currency' => $a->currency,
                'opening_balance' => (float) $a->opening_balance,
                'opening_balance_pkr' => BudgetSetting::toPkr((float) $a->opening_balance, $a->currency ?? 'PKR'),
                'current_balance_pkr' => (float) $a->current_balance_pkr,
            ]);

        return self::success($accounts);
    }
}

