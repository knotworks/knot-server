<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;

/**
 * @group Notifications
 *
 * Read and manage the authenticated user's notifications.
 *
 * @authenticated
 */
class NotificationsController extends Controller
{
    /**
     * List notifications.
     *
     * @response 200 {"data":[{"id":"db-notification-id","type":"App\\Notifications\\AddedPost"}],"links":[],"meta":[]}
     */
    public function index()
    {
        return auth()->user()->notifications()->latest()->paginate(10);
    }

    /**
     * Mark all notifications as read.
     *
     * @response 204 {}
     */
    public function destroy()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => Carbon::now()]);

        return response()->noContent();
    }
}
