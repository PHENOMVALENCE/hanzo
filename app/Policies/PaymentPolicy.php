<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('buyer')) {
            return $payment->order->buyer_id === $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('buyer');
    }
}
