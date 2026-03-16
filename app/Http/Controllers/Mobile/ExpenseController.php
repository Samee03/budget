<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BudgetSetting;
use App\Models\Expense;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Expense::query()->orderByDesc('spent_at');

        if ($request->filled('from')) {
            $query->whereDate('spent_at', '>=', Carbon::parse($request->query('from')));
        }

        if ($request->filled('to')) {
            $query->whereDate('spent_at', '<=', Carbon::parse($request->query('to')));
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->query('account_id'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }

        if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->query('expense_category_id'));
        }

        $perPage = (int) $request->query('per_page', 20);

        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(function (Expense $expense) {
            $amountPkr = BudgetSetting::toPkr((float) $expense->amount, $expense->currency ?? 'PKR');

            return [
                'id' => $expense->id,
                'spent_at' => $expense->spent_at?->toDateString(),
                'amount' => (float) $expense->amount,
                'currency' => $expense->currency,
                'amount_pkr' => $amountPkr,
                'category' => $expense->expenseCategory?->name,
                'expense_category_id' => $expense->expense_category_id,
                'payee_name' => $expense->payee_name,
                'description' => $expense->description,
                'payment_method' => $expense->payment_method,
                'project_id' => $expense->project_id,
                'project_name' => $expense->project?->name,
                'account_id' => $expense->account_id,
                'account_name' => $expense->account?->name,
            ];
        });

        return self::success([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'spent_at' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['PKR', 'USD'])],
            'expense_category_id' => ['nullable', 'integer', 'exists:expense_categories,id'],
            'payee_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        $expense = Expense::create([
            'spent_at' => $data['spent_at'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'expense_category_id' => $data['expense_category_id'] ?? null,
            'payee_name' => $data['payee_name'] ?? null,
            'description' => $data['description'],
            'payment_method' => $data['payment_method'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'account_id' => $data['account_id'] ?? null,
        ]);

        return self::success(['id' => $expense->id], 'Expense created', 201);
    }
}

