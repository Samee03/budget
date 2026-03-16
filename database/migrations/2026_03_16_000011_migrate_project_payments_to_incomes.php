<?php

use App\Models\Income;
use App\Models\ProjectPayment;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        /** @var \Illuminate\Support\Collection<int, ProjectPayment> $payments */
        $payments = ProjectPayment::query()->get();

        foreach ($payments as $payment) {
            Income::query()->create([
                'account_id' => $payment->account_id,
                'project_id' => $payment->project_id,
                'income_type_id' => null,
                'received_at' => $payment->received_at,
                'amount' => $payment->amount,
                'currency' => $payment->currency ?? 'USD',
                'fx_rate_to_pkr' => $payment->fx_rate_to_pkr,
                'amount_in_pkr' => $payment->amount_in_pkr,
                'source' => $payment->project?->name,
                'description' => null,
                'payment_method' => $payment->method,
                'payment_reference' => $payment->reference,
                'income_kind' => 'project_payment',
                'notes' => $payment->notes,
            ]);
        }
    }

    public function down(): void
    {
        // Best-effort rollback: delete incomes that originated from project payments.
        Income::query()
            ->where('income_kind', 'project_payment')
            ->delete();
    }
};

