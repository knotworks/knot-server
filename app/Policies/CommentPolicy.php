<?php

namespace Knot\Policies;

use Knot\Models\User;
use Knot\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function can_modify_or_delete(User $user, Comment $comment)
    {
        return $user->id == $comment->user_id;
    }
}
