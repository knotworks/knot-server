<?php

namespace Knot\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'profile_image',
        'cover_image',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'profile_image',
    ];

    protected $appends = [
        'full_name',
        'avatar_url',
    ];

    /**
     * Hash the user's password.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getFullNameAttribute($value)
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->profile_image) {
            return $this->profile_image;
        } else {
            return 'https://placekitten.com/400/400';
        }
    }

    /**
     * Fetch an activity feed for the given user.
     *
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function timeline()
    {
        $ids = $this->getFriends()->map->id->prepend($this->id);

        return Post::with(['location', 'postable', 'user', 'comments', 'reactions.user', 'accompaniments.user'])
            ->latest()
            ->whereIn('user_id', $ids)
            ->paginate(config('app.posts_per_page'));
    }

    public function getSuggestedFriends()
    {
        $ids = [$this->id];
        $this->getAllFriendships()->each(function ($friendship) use (&$ids) {
            array_push($ids, $friendship->sender_id, $friendship->recipient_id);
        });
        $idsToExclude = collect($ids)->unique()->values()->all();

        return self::whereNotIn('id', $idsToExclude)->get();
    }
}
