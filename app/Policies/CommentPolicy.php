<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function can_modify_or_delete(User $user, Comment $comment)
    {
        return $user->isAdmin() || $user->id == $comment->user_id;
    }
}
