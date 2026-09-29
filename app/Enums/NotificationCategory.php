<?php

namespace App\Enums;

enum NotificationCategory: int
{
    case FOLLOW = 10;
    case POST_APPROVED = 20;
    case EVALUATION_APPROVED = 30;
    case POST_LIKED = 40;
    case POST_COMMENTED = 50;
}
