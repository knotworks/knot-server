<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
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
        return $user->isAdmin() || $user->id == $post->user_id;
    }
}
