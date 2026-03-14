<?php

declare(strict_types=1);

namespace App\Http\Controllers;

/**
 * @group Timeline
 *
 * View the authenticated user's timeline feed.
 *
 * @authenticated
 */
class TimelineController extends Controller
{
    /**
     * Fetch the timeline feed.
     *
     * @response 200 [{"id":1,"body":"A new family update","user_id":1}]
     */
    public function show()
    {
        return auth()->user()->timeline();
    }
}
