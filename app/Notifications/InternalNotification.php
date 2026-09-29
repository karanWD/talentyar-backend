<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Models\Comment;
use App\Models\Evaluation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InternalNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly NotificationCategory $category,
        private readonly string $title,
        private readonly string $message,
        private readonly array $payload = [],
    ) {}

    public static function followedBy(User $follower): self
    {
        return new self(
            NotificationCategory::FOLLOW,
            'دنبال‌کننده جدید',
            self::userDisplayName($follower).' شما را دنبال کرد.',
            [
                'user_id' => $follower->id,
                'username' => $follower->username,
            ],
        );
    }

    public static function postApproved(Post $post): self
    {
        return new self(
            NotificationCategory::POST_APPROVED,
            'انتشار پست',
            'پست شما تأیید و منتشر شد.',
            ['post_id' => $post->id],
        );
    }

    public static function evaluationApproved(Evaluation $evaluation): self
    {
        return new self(
            NotificationCategory::EVALUATION_APPROVED,
            'تأیید ارزیابی',
            'ارزیابی شما تأیید شد.',
            ['evaluation_id' => $evaluation->id],
        );
    }

    public static function postLiked(Post $post, User $actor): self
    {
        return new self(
            NotificationCategory::POST_LIKED,
            'لایک جدید',
            self::userDisplayName($actor).' پست شما را لایک کرد.',
            [
                'post_id' => $post->id,
                'user_id' => $actor->id,
                'username' => $actor->username,
            ],
        );
    }

    public static function postCommented(Post $post, Comment $comment, User $actor): self
    {
        return new self(
            NotificationCategory::POST_COMMENTED,
            'کامنت جدید',
            self::userDisplayName($actor).' برای پست شما کامنت گذاشت.',
            [
                'post_id' => $post->id,
                'comment_id' => $comment->id,
                'user_id' => $actor->id,
                'username' => $actor->username,
            ],
        );
    }

    private static function userDisplayName(User $user): string
    {
        $name = trim($user->first_name.' '.$user->last_name);

        return $name !== '' ? $name : ($user->username ?: 'یک کاربر');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'category' => $this->category->value,
            'title' => $this->title,
            'message' => $this->message,
            'payload' => $this->payload,
        ];
    }
}
