<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Knot\Traits\KnotFriendable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, KnotFriendable, HasFactory, HasApiTokens;

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
    ];

    protected $appends = [
        'full_name',
    ];

    public function routeNotificationForTelegram()
    {
        return $this->telegram_user_id;
    }

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

        return Post::with(['location', 'user', 'comments', 'reactions.user', 'accompaniments.user', 'media'])
            ->latest()
            ->whereIn('user_id', $ids)
            ->paginate(config('app.posts_per_page'));
    }

    public function feed()
    {
        return Post::with(['location', 'user', 'comments', 'reactions.user', 'accompaniments.user', 'media'])
            ->latest()
            ->where('user_id', $this->id)
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
