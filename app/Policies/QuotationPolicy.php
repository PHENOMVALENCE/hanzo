<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function view(User $user, Quotation $quotation): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('buyer')) {
            return $quotation->rfq->buyer_id === $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Quotation $quotation): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('buyer')) {
            return $quotation->rfq->buyer_id === $user->id;
        }
        return false;
    }
}
