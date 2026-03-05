<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('buyer')) {
            return $document->order->buyer_id === $user->id;
        }
        if ($user->hasRole('factory')) {
            $factoryId = $document->order->quotation->rfq->assigned_factory_id ?? null;
            return $user->factory && $user->factory->id === $factoryId;
        }
        return false;
    }
}
