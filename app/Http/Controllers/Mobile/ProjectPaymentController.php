<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectPaymentController extends Controller
{
    use ApiResponse;

    public function store(Project $project, Request $request)
    {
        $data = $request->validate([
            'received_at' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['PKR', 'USD'])],
            'fx_rate_to_pkr' => ['nullable', 'numeric', 'min:0'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
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

        $income = Income::query()->create([
            'project_id' => $project->id,
            'account_id' => $data['account_id'] ?? null,
            'received_at' => $data['received_at'],
            'amount' => $amount,
            'currency' => $currency,
            'fx_rate_to_pkr' => $data['fx_rate_to_pkr'] ?? null,
            'amount_in_pkr' => $amountInPkr,
            'payment_method' => $data['method'] ?? null,
            'payment_reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'income_kind' => 'project_payment',
            'source' => $project->name,
        ]);

        return self::success(['id' => $income->id], 'Project payment created', 201);
    }
}

