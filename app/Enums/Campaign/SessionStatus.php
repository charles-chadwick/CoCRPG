<?php

namespace App\Enums\Campaign;

enum SessionStatus: string
{
    case Scheduled = 'Scheduled';
    case Played = 'Played';
    case Cancelled = 'Cancelled';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
