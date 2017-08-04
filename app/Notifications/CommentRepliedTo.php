<?php

namespace Knot\Notifications;

use Illuminate\Notifications\Notification;
use Knot\Models\Comment;

class CommentRepliedTo extends Notification
{
    protected $comment;
    /**
     * Create a new notification instance.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
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
            'comment' => $this->comment->load('user', 'post.user')
        ];
    }
}
