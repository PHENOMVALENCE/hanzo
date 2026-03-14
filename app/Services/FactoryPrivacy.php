<?php

namespace App\Services;

use App\Models\Factory;
use Illuminate\Support\Collection;

/**
 * Enforces Factory Visibility Rule: Buyers must NOT see factory direct contact details.
 * They may only see "HANZO Verified Factory" and general production information.
 */
class FactoryPrivacy
{
    /** Contact fields that must never be exposed to buyers */
    public const CONTACT_FIELDS = ['contact_wechat', 'contact_phone', 'contact_email'];

    /**
     * Sanitize factory for buyer display. Strips all direct contact details.
     * Returns a DTO-like array safe for buyer views.
     */
    public static function forBuyer(Factory $factory): array
    {
        return [
            'id' => $factory->id,
            'display_name' => 'HANZO Verified Factory',
            'verification_status' => $factory->verification_status,
            'categories' => $factory->categories ?? [],
            'location_region' => 'China',
            'notes' => null,
        ];
    }

    /**
     * Sanitize a collection of factories for buyer display.
     */
    public static function forBuyerCollection(Collection $factories): Collection
    {
        return $factories->map(fn (Factory $f) => (object) self::forBuyer($f));
    }

    /**
     * Hide contact fields on a Factory model when passing to buyer views.
     * Use for view data — prefers forBuyer() for structured display.
     */
    public static function hideContacts(Factory $factory): Factory
    {
        $factory->contact_wechat = null;
        $factory->contact_phone = null;
        $factory->contact_email = null;
        $factory->setRelation('user', null);

        return $factory;
    }
}
