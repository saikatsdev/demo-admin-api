<?php

namespace App\Enums;

enum DiscountTypeEnum: string
{
    const FIXED      = "fixed";
    const PERCENTAGE = "percentage";

    public static function getAll() : array
    {
        return [
            self::FIXED,
            self::PERCENTAGE,
        ];
    }
}
