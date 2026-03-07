<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Traits\ApiResponse;

class ProjectController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $projects = Project::query()
            ->with('client')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn (Project $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'client_name' => $p->client?->name,
                'currency' => $p->currency,
                'total_amount' => (float) $p->total_amount,
                'total_paid' => (float) $p->total_paid,
                'remaining_amount' => (float) $p->remaining_amount,
                'status' => $p->status,
                'start_date' => $p->start_date?->toDateString(),
                'end_date' => $p->end_date?->toDateString(),
            ]);

        return self::success($projects);
    }
}

