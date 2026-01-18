<?php

namespace App\Enums;

enum StatusEnum : string
{
    const PAID     = "paid";
    const UNPAID   = "unpaid";
    const ACTIVE   = "active";
    const INACTIVE = "inactive";
    const PENDING  = "pending";
    const RECEIVED = "received";
    const CANCELED = "canceled";

    public static function paidStatus(): array
    {
        return [
            self::PAID,
            self::UNPAID,
        ];
    }

    public static function activeStatus(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }

    public static function purchaseStatus(): array
    {
        return [
            self::PENDING,
            self::RECEIVED,
            self::CANCELED,
        ];
    }
}
