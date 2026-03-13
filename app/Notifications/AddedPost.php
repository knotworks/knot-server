<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class AddedPost extends Notification
{
    protected $reaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'post' => $this->post->load('user'),
        ];
    }

    public function toTelegram($notifiable)
    {
        $isPhotoPost = $this->post->media()->where('status', 'ready')->exists();
        $postAuthor = $this->post->user->first_name;
        $postBody = $this->post->body;
        $messageBody = '*' . $postAuthor . "* added a post. \n _" . $postBody . '_';

        if ($isPhotoPost) {
            $postMedia = $this->post->media
                ->where('status', 'ready')
                ->map(function ($media) {
                    return $media->url;
                })
                ->filter()
                ->join("\n\n");

            $messageBody = '*' . $postAuthor . "* added a photo. \n _" . $postBody . "_ \n" . $postMedia;
        }

        return TelegramMessage::create()->content($messageBody);
    }
}
