<?php

namespace App\Enums;

enum ReturnOrDamageTypeEnum: string
{
    case FULL    = "full";
    case PARTIAL = "partial";

    public static function getAll(): array
    {
        return [
            self::FULL->value,
            self::PARTIAL->value,
        ];
    }
}
