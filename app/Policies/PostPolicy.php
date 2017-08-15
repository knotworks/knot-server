<?php

namespace Knot\Policies;

use Knot\Models\User;
use Knot\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function can_view_post(User $user, Post $post)
    {
        return $user->isFriendWith($post->user) || $user->id == $post->user_id;
    }

    public function can_modify_or_delete_post(User $user, Post $post)
    {
        return $user->id == $post->user_id;
    }
}
