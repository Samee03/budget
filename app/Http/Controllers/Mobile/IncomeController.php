<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BudgetSetting;
use App\Models\Income;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Income::query()->orderByDesc('received_at');

        if ($request->filled('from')) {
            $query->whereDate('received_at', '>=', Carbon::parse($request->query('from')));
        }

        if ($request->filled('to')) {
            $query->whereDate('received_at', '<=', Carbon::parse($request->query('to')));
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->query('account_id'));
        }

        if ($request->filled('income_type_id')) {
            $query->where('income_type_id', $request->query('income_type_id'));
        }

        $perPage = (int) $request->query('per_page', 20);

        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(function (Income $income) {
            $amountPkr = $income->amount_in_pkr !== null
                ? (float) $income->amount_in_pkr
                : BudgetSetting::toPkr((float) $income->amount, $income->currency ?? 'PKR');

            return [
                'id' => $income->id,
                'received_at' => $income->received_at?->toDateString(),
                'amount' => (float) $income->amount,
                'currency' => $income->currency,
                'amount_pkr' => $amountPkr,
                'type' => $income->incomeType?->name,
                'income_type_id' => $income->income_type_id,
                'source' => $income->source,
                'description' => $income->description,
                'payment_method' => $income->payment_method,
                'account_id' => $income->account_id,
                'account_name' => $income->account?->name,
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
            'received_at' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['PKR', 'USD'])],
            'fx_rate_to_pkr' => ['nullable', 'numeric', 'min:0'],
            'income_type_id' => ['required', 'integer', 'exists:income_types,id'],
            'source' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        $amount = (float) $data['amount'];
        $currency = $data['currency'] ?? 'PKR';
        $rate = (float) ($data['fx_rate_to_pkr'] ?? 0);

        $amountInPkr = null;
        if ($currency === 'PKR') {
            $amountInPkr = $amount;
        } elseif ($currency === 'USD' && $amount && $rate) {
            $amountInPkr = $amount * $rate;
        }

        $income = Income::create([
            'account_id' => $data['account_id'] ?? null,
            'income_type_id' => $data['income_type_id'],
            'received_at' => $data['received_at'],
            'amount' => $amount,
            'currency' => $currency,
            'fx_rate_to_pkr' => $data['fx_rate_to_pkr'] ?? null,
            'amount_in_pkr' => $amountInPkr,
            'source' => $data['source'] ?? null,
            'description' => $data['description'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => null,
        ]);

        return self::success(['id' => $income->id], 'Income created', 201);
    }
}

