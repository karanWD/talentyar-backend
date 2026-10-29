<?php

namespace App\Enums;

enum ChallengeType: string
{
    case Score = 'score';
    case Action = 'action';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
