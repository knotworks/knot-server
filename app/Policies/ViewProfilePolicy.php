<?php

namespace Knot\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Knot\Models\User;

class ViewProfilePolicy
{
    use HandlesAuthorization;

    public function can_view_profile(User $user, User $profile)
    {
        $friends = $user->getFriends();

        if ($user->id == $profile->id) {
            return true;
        }
        if (! $friends->count()) {
            return false;
        }

        return $user->getFriends()->map->id->push($user->id)->contains($profile->id);
    }
}
