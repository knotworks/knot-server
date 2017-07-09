<?php

namespace Knot\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

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
     *
     */
    public function destroy()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => Carbon::now()]);
    }
}
