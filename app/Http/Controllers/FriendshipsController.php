<?php

namespace Knot\Http\Controllers;

use Knot\Models\User;
use Illuminate\Http\Request;
use Hootlex\Friendships\Models\Friendship;
use Knot\Notifications\AddedAsFriend;
use Knot\Notifications\FriendRequestAccepted;

class FriendshipsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Return a user's friendship data
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }

    /**
     * Send a friend request to another user
     *
     * @param Request $request
     * @param User $recipient
     * @return \Illuminate\Http\Response
     */
    public function addFriend(Request $request, User $recipient)
    {
        auth()->user()->befriend($recipient);
        $recipient->notify(new AddedAsFriend(auth()->user()));

        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }

    /**
     * Accept a friend request from another user
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\User $sender
     * @return \Illuminate\Http\Response
     */
    public function acceptFriendship(Request $request, User $sender)
    {
        auth()->user()->acceptFriendRequest($sender);
        $sender->notify(new FriendRequestAccepted(auth()->user()));

        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }

    /**
     * Deny a friend request from another user
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\User $sender
     * @return \Illuminate\Http\Response
     */
    public function denyFriendship(Request $request, User $sender)
    {
        auth()->user()->denyFriendRequest($sender);

        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }

    /**
     * Unfriend a user
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\User $friend
     * @return \Illuminate\Http\Response
     */
    public function unfriend(Request $request, User $friend)
    {
        auth()->user()->unfriend($friend);

        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }
}
