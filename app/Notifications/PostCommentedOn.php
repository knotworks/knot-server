<?php

namespace Knot\Notifications;

use Knot\Models\Post;
use Knot\Models\Comment;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class PostCommentedOn extends Notification
{
    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        if ($notifiable->telegram_user_id) {
            $channels[] = TelegramChannel::class;
        }

        return $channels;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'comment' => $this->comment->load('user', 'post'),
        ];
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->content('*'.$this->comment->user->first_name."* commented on your post: \n _".$this->comment->body.'_');
    }
}
