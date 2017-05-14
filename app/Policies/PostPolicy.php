<?php

namespace FamJam\Policies;

use FamJam\Models\User;
use FamJam\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function can_react(User $user, Post $post)
    {
        return $user->isFriendWith($post->postable->user) || $user->id == $post->postable->user_id;
    }

    public function can_comment(User $user, Post $post)
    {
        return $user->isFriendWith($post->postable->user) || $user->id == $post->postable->user_id;
    }

    public function can_view_comments(User $user, Post $post)
    {
        return $user->isFriendWith($post->postable->user) || $user->id == $post->postable->user_id;
    }
}
