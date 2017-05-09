<?php

namespace FamJam\Policies;

use FamJam\Models\User;
use FamJam\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function can_react(User $user, Post $post)
    {
        return $user->isFriendWith($post->postable->user) || $user->id == $post->postable->user->id;
    }
}
