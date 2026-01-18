<?php

namespace App\Enums;

enum BannerEnum : string
{
    const DESKTOP = "desktop";
    const MOBILE  = "mobile";

    public static function getAll(): array
    {
        return [
            self::DESKTOP,
            self::MOBILE
        ];
    }
}
