<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BudgetSetting;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function show(Request $request)
    {
        $start = $request->query('from');
        $end = $request->query('to');

        $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();

        // Project payments in period
        $paymentIncomePkr = ProjectPayment::query()
            ->whereBetween('received_at', [$startDate, $endDate])
            ->get()
            ->sum(fn (ProjectPayment $p) => $p->amount_in_pkr !== null
                ? (float) $p->amount_in_pkr
                : BudgetSetting::toPkr((float) $p->amount, $p->currency ?? 'USD')
            );

        // Other incomes in period
        $otherIncomePkr = Income::query()
            ->whereBetween('received_at', [$startDate, $endDate])
            ->get()
            ->sum(fn (Income $i) => $i->amount_in_pkr !== null
                ? (float) $i->amount_in_pkr
                : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
            );

        $totalIncomePkr = $paymentIncomePkr + $otherIncomePkr;

        // Expenses in period
        $totalExpensesPkr = Expense::query()
            ->whereBetween('spent_at', [$startDate, $endDate])
            ->get()
            ->sum(fn (Expense $e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'PKR'));

        $netPkr = $totalIncomePkr - $totalExpensesPkr;

        // Opening balances from accounts
        $openingBalancePkr = Account::query()->get()->sum(
            fn (Account $a) => BudgetSetting::toPkr((float) $a->opening_balance, $a->currency ?? 'PKR')
        );

        // All-time income/expenses for current balance
        $allTimePaymentIncomePkr = ProjectPayment::query()->get()->sum(
            fn (ProjectPayment $p) => $p->amount_in_pkr !== null
                ? (float) $p->amount_in_pkr
                : BudgetSetting::toPkr((float) $p->amount, $p->currency ?? 'USD')
        );
        $allTimeOtherIncomePkr = Income::query()->get()->sum(
            fn (Income $i) => $i->amount_in_pkr !== null
                ? (float) $i->amount_in_pkr
                : BudgetSetting::toPkr((float) $i->amount, $i->currency ?? 'PKR')
        );
        $allTimeExpensesPkr = Expense::query()->get()->sum(
            fn (Expense $e) => BudgetSetting::toPkr((float) $e->amount, $e->currency ?? 'PKR')
        );

        $currentBalancePkr = $openingBalancePkr + $allTimePaymentIncomePkr + $allTimeOtherIncomePkr - $allTimeExpensesPkr;

        $accounts = Account::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Account $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'currency' => $a->currency,
                'opening_balance' => (float) $a->opening_balance,
                'current_balance_pkr' => (float) $a->current_balance_pkr,
            ]);

        $activeProjectsCount = Project::query()->where('status', 'active')->count();

        return self::success([
            'period' => [
                'from' => $startDate->toDateString(),
                'to' => $endDate->toDateString(),
            ],
            'income_pkr' => $totalIncomePkr,
            'expenses_pkr' => $totalExpensesPkr,
            'net_pkr' => $netPkr,
            'opening_balance_pkr' => $openingBalancePkr,
            'current_balance_pkr' => $currentBalancePkr,
            'accounts' => $accounts,
            'active_projects' => $activeProjectsCount,
        ]);
    }
}

