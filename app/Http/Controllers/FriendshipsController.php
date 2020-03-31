<?php

namespace Knot\Http\Controllers;

use Hootlex\Friendships\Models\Friendship;
use Illuminate\Http\Request;
use Knot\Models\User;
use Knot\Notifications\AddedAsFriend;
use Knot\Notifications\FriendRequestAccepted;

class FriendshipsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    /**
     * Return a user's friendship data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getFriendships();
    }

    /**
     * Send a friend request to another user.
     *
     * @param Request $request
     * @param User    $recipient
     *
     * @return \Illuminate\Http\Response
     */
    public function addFriend(Request $request, User $recipient)
    {
        auth()->user()->befriend($recipient);
        $recipient->notify(new AddedAsFriend(auth()->user()));

        return $this->getFriendships();
    }

    /**
     * Accept a friend request from another user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\User        $sender
     *
     * @return \Illuminate\Http\Response
     */
    public function acceptFriendship(Request $request, User $sender)
    {
        auth()->user()->acceptFriendRequest($sender);
        $sender->notify(new FriendRequestAccepted(auth()->user()));

        return $this->getFriendships();
    }

    /**
     * Deny a friend request from another user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\User        $sender
     *
     * @return \Illuminate\Http\Response
     */
    public function denyFriendship(Request $request, User $sender)
    {
        auth()->user()->denyFriendRequest($sender);

        return $this->getFriendships();
    }

    /**
     * Unfriend a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\User        $friend
     *
     * @return \Illuminate\Http\Response
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
