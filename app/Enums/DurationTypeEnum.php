<?php

namespace App\Enums;

enum DurationTypeEnum: string
{
    const MINUTES = "minutes";
    const HOURS   = "hours";
    const DAYS    = "days";

    public static function getAll(): array
    {
        return [
            self::MINUTES,
            self::HOURS,
            self::DAYS
        ];
    }
}
