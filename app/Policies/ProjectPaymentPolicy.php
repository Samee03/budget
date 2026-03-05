<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\ProjectPayment;

class ProjectPaymentPolicy
{
    public function viewAny(?Admin $user = null): bool
    {
        return true;
    }

    public function view(?Admin $user = null, ProjectPayment $payment): bool
    {
        return true;
    }

    public function create(?Admin $user = null): bool
    {
        return true;
    }

    public function update(?Admin $user = null, ProjectPayment $payment): bool
    {
        return true;
    }

    public function delete(?Admin $user = null, ProjectPayment $payment): bool
    {
        return true;
    }

    public function restore(?Admin $user = null, ProjectPayment $payment): bool
    {
        return true;
    }

    public function forceDelete(?Admin $user = null, ProjectPayment $payment): bool
    {
        return true;
    }
}

