<?php

namespace Knot\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hootlex\Friendships\Traits\Friendable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    protected $appends = ['full_name'];

    /**
     * Hash the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute($value) {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Fetch an activity feed for the given user.
     *
     * @param  User $user
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
