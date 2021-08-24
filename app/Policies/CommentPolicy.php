<?php

namespace Knot\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Knot\Models\Comment;
use Knot\Models\User;

class CommentPolicy
{
    use HandlesAuthorization;

    public function can_modify_or_delete(User $user, Comment $comment)
    {
        return $user->isAdmin() || $user->id == $comment->user_id;
    }
}
