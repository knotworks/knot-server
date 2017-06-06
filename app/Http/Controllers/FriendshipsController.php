<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use FamJam\Models\User;
use Hootlex\Friendships\Models\Friendship;

class FriendshipsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index()
    {
        return auth()->user()->getAllFriendships();
    }

    public function acceptFriendship(Request $request, User $sender)
    {
        auth()->user()->acceptFriendRequest($sender);

        return auth()->user()->getAllFriendships();
    }

    public function denyFriendship(Request $request, User $sender)
    {
        auth()->user()->denyFriendRequest($sender);

        return auth()->user()->getAllFriendships();
    }
}
