<?php

namespace Knot\Notifications;

use Illuminate\Notifications\Notification;
use Knot\Models\User;

class AddedAsFriend extends Notification
{
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @param  User  $sender
     */
    public function __construct(User $sender)
    {
        $this->sender = $sender;
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
            'sender' => $this->sender,
        ];
    }
}
