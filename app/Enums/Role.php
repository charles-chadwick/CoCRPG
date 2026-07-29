<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'Admin';
    case GameMaster = 'Game Master';
    case Player = 'Player';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
