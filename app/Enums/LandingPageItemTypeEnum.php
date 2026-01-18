<?php

namespace App\Enums;

enum LandingPageItemTypeEnum : string
{
    const NORMAL  = "normal";
    const RELATED = "related";

    public static function getAll() : array
    {
        return [
            self::NORMAL,
            self::RELATED,
        ];
    }
}
