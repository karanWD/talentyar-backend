<?php

namespace App\Enums;

enum ChallengeActionType: string
{
    case Like = 'like';
    case Follower = 'follower';
    case Following = 'following';
    case Comment = 'comment';
    case Post = 'post';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
