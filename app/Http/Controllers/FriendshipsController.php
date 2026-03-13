<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AddedAsFriend;
use App\Notifications\FriendRequestAccepted;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FriendshipsController extends Controller
{
    /**
     * Return a user's friendship data.
     *
     * @return Response
     */
    public function index()
    {
        return $this->getFriendships();
    }

    /**
     * Send a friend request to another user.
     *
     *
     * @return Response
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
     *
     * @return Response
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
     *
     * @return Response
     */
    public function denyFriendship(Request $request, User $sender)
    {
        auth()->user()->denyFriendRequest($sender);

        return $this->getFriendships();
    }

    /**
     * Unfriend a user.
     *
     *
     * @return Response
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
