<?php

declare(strict_types=1);

namespace NotificationChannels\Telegram;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TelegramChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTelegram')) {
            return;
        }

        $chatId = $notifiable->routeNotificationFor('telegram', $notification);

        if (! $chatId || blank(config('services.telegram-bot-api.token'))) {
            return;
        }

        $message = $notification->toTelegram($notifiable);

        if (! $message instanceof TelegramMessage || blank($message->content)) {
            return;
        }

        Http::asForm()->post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
            'chat_id' => $chatId,
            ...$message->toArray(),
        ])->throw();
    }
}
