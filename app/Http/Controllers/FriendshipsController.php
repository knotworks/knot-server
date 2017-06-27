<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Knot\Models\User;
use Hootlex\Friendships\Models\Friendship;

class FriendshipsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index()
    {
        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }

    public function acceptFriendship(Request $request, User $sender)
    {
        auth()->user()->acceptFriendRequest($sender);

        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }

    public function denyFriendship(Request $request, User $sender)
    {
        auth()->user()->denyFriendRequest($sender);

        return auth()->user()->getAllFriendships()->load('sender', 'recipient');
    }
}
