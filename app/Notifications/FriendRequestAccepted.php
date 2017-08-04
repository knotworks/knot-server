<?php

namespace Knot\Notifications;

use Illuminate\Notifications\Notification;
use Knot\Models\User;

class FriendRequestAccepted extends Notification
{
    protected $recipient;
    /**
     * Create a new notification instance.
     *
     * @param  User  $recipient
     * @return void
     */
    public function __construct(User $recipient)
    {
        $this->recipient = $recipient;
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
            'recipient' => $this->recipient,
        ];
    }
}
