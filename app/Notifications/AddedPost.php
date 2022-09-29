<?php

namespace Knot\Notifications;

use Cloudinary\Cloudinary;
use Illuminate\Notifications\Notification;
use Knot\Models\Post;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class AddedPost extends Notification
{
    protected $reaction;

    /**
     * Create a new notification instance.
     *
     * @param  Post  $post
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
        $isPhotoPost = $this->post->media()->exists();
        $postAuthor = $this->post->user->first_name;
        $postBody = $this->post->body;
        $messageBody = '*'.$postAuthor."* added a post. \n _".$postBody.'_';

        if ($isPhotoPost) {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.key'),
                    'api_secret' => config('services.cloudinary.secret'),
                ],
            ]);

            $postMedia = $this->post->media->map(function ($media) use ($cloudinary) {
                return $cloudinary->image($media->path)->toUrl();
            })->join("\n\n");

            $messageBody = '*'.$postAuthor."* added a photo. \n _".$postBody."_ \n".$postMedia;
        }

        return TelegramMessage::create()->content($messageBody);
    }
}
