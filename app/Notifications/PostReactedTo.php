<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Reaction;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class PostReactedTo extends Notification
{
    protected $reaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reaction $reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
            'reaction' => $this->reaction->load('user', 'post'),
        ];
    }

    public function toTelegram($notifiable)
    {
        $reactions = [
            'smile' => '🙂',
            'love' => '😍',
            'frown' => '☹️',
            'surprise' => '😮',
            'laugh' => '😆',
            'angry' => '😡',
        ];

        return TelegramMessage::create()
            ->content('*' . $this->reaction->user->first_name . '* ' . $reactions[$this->reaction->type] . ' at your post.');
    }
}
