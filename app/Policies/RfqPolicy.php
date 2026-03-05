<?php

namespace App\Policies;

use App\Models\Rfq;
use App\Models\User;

class RfqPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Rfq $rfq): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('buyer')) {
            return $rfq->buyer_id === $user->id;
        }
        if ($user->hasRole('factory')) {
            $factory = $user->factory;
            return $factory && $rfq->assigned_factory_id === $factory->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('buyer');
    }

    public function update(User $user, Rfq $rfq): bool
    {
        return $user->hasRole('admin');
    }
}
