<?php

namespace Knot\Notifications;

use Illuminate\Notifications\Notification;
use Knot\Models\Reaction;

class PostReactedTo extends Notification
{
    protected $reaction;
    /**
     * Create a new notification instance.
     * @param  Reaction  $reaction
     * @return void
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
            'reaction' => $this->reaction->load('user', 'post'),
        ];
    }
}
