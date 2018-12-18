<?php

namespace Knot\Notifications;

use Knot\Models\Post;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Notifications\Notification;

class AddedPost extends Notification
{
    protected $reaction;

    /**
     * Create a new notification instance.
     *
     * @param Post $post
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
            'post' => $this->post->load('user', 'postable'),
        ];
    }

    public function toTelegram($notifiable)
    {
        $isPhotoPost = $this->post->postable_type == 'Knot\Models\PhotoPost';
        $author = $this->post->user->first_name;
        $body = $this->post->postable->body;
        if ($isPhotoPost) {
            $photo = $this->post->postable->image_url;
            return TelegramFile::create()
                ->content('*' . $author . '* added a photo. \n _' . $body . '_')
                ->file($photo, 'photo');
        } else {
            return TelegramMessage::create()
                ->content('*' . $author . '* added a post. \n _' . $body . '_');
        }
    }
}
