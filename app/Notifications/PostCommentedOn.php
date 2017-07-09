<?php

namespace Knot\Notifications;

use Illuminate\Notifications\Notification;

class PostCommentedOn extends Notification
{
    protected $post;
    protected $comment;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post, $comment)
    {
        $this->post = $post;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'body' => "{$this->comment->user->first_name}: {$this->comment->body}",
            'post_id' => $this->post->id,
        ];
    }
}
