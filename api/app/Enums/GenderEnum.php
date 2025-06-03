<?php

namespace App\Enums;

enum GenderEnum: string
{
    case Male = 'male';
    case Female = 'female';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
