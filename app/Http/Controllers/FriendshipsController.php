<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AddedAsFriend;
use App\Notifications\FriendRequestAccepted;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Friendships
 *
 * Send, accept, deny, and inspect friendship relationships.
 *
 * @authenticated
 */
class FriendshipsController extends Controller
{
    /**
     * Fetch the current user's friendship data.
     *
     * @response 200 {"friends":[],"requests":[],"outgoing":[],"suggested":[]}
     */
    public function index()
    {
        return $this->getFriendships();
    }

    /**
     * Send a friend request.
     *
     * @urlParam recipient integer required The recipient user ID. Example: 2
     *
     * @response 200 {"friends":[],"requests":[],"outgoing":[{"recipient_id":2}],"suggested":[]}
     */
    public function addFriend(Request $request, User $recipient)
    {
        auth()->user()->befriend($recipient);

        $recipient->notify(new AddedAsFriend(auth()->user()));

        return $this->getFriendships();
    }

    /**
     * Accept a friend request.
     *
     * @urlParam sender integer required The sender user ID. Example: 2
     *
     * @response 200 {"friends":[{"id":2}],"requests":[],"outgoing":[],"suggested":[]}
     */
    public function acceptFriendship(Request $request, User $sender)
    {
        auth()->user()->acceptFriendRequest($sender);

        $sender->notify(new FriendRequestAccepted(auth()->user()));

        return $this->getFriendships();
    }

    /**
     * Deny a friend request.
     *
     * @urlParam sender integer required The sender user ID. Example: 2
     *
     * @response 200 {"friends":[],"requests":[],"outgoing":[],"suggested":[]}
     */
    public function denyFriendship(Request $request, User $sender)
    {
        auth()->user()->denyFriendRequest($sender);

        return $this->getFriendships();
    }

    /**
     * Remove an existing friend.
     *
     * @urlParam friend integer required The friend user ID. Example: 2
     *
     * @response 200 {"friends":[],"requests":[],"outgoing":[],"suggested":[]}
     */
    public function unfriend(Request $request, User $friend)
    {
        auth()->user()->unfriend($friend);

        return $this->getFriendships();
    }

    private function getFriendships()
    {
        return [
            'friends' => auth()->user()->getFriends(),
            'requests' => auth()->user()->getFriendRequests()->load('sender'),
            'outgoing' => auth()->user()->getPendingFriendships()->where('recipient_id', '!=', auth()->id())->load('recipient'),
            'suggested' => auth()->user()->getSuggestedFriends(),
        ];
    }
}
