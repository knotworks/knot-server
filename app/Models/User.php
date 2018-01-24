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
        $path = config('filesystems.disks.b2.basePath');
        $bucket = config('filesystems.disks.b2.bucketName');
        $imagePath = $this->profile_image;
        if (! $imagePath) {
            return;
        }
        if (starts_with($imagePath, 'avatars')) {
            return "{$path}{$bucket}/{$imagePath}";
        } else {
            return asset('images/tmp/'.pathinfo($imagePath, PATHINFO_BASENAME));
        }
    }

    /**
     * Fetch an activity feed for the given user.
     *
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function feed()
    {
        $ids = $this->getFriends()->map->id->prepend($this->id);

        return Post::with(['location', 'postable', 'user', 'comments', 'reactions.user'])
            ->latest()
            ->whereIn('user_id', $ids)
            ->get();
    }
}
