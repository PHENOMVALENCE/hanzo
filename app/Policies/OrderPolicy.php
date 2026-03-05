<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('buyer')) {
            return $order->buyer_id === $user->id;
        }
        if ($user->hasRole('factory')) {
            $factoryId = $order->quotation->rfq->assigned_factory_id ?? null;
            return $user->factory && $user->factory->id === $factoryId;
        }
        return false;
    }
}
