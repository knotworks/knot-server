<?php

namespace Knot\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Knot\Models\Post;
use Knot\Models\User;

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
