<?php
namespace Knot\Policies;

use Knot\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ViewProfilePolicy
{
  use HandlesAuthorization;

  public function can_view_profile(User $user, User $profile)
  {
    $friends = $user->getFriends();

    if ($user->id == $profile->id) return true;
    if (!count($friends->all())) return false;



    $ids = $user->getFriends()->map->id->push($user->id);
    return $ids->contains($profile->id);
  }
}
