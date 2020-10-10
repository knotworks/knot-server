<?php

namespace Knot\Http\Controllers;

use Carbon\Carbon;

class NotificationsController extends Controller
{
    /**
     * Fetch all notifications for the user.
     *
     * @return mixed
     */
    public function index()
    {
        return auth()->user()->notifications;
    }

    /**
     * Mark all notifications as read.
     */
    public function destroy()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => Carbon::now()]);

        return response([], 204);
    }
}
