<?php

namespace FamJam\Models;

use Illuminate\Database\Eloquent\Model;
use FamJam\Traits\Locatable;

class Post extends Model
{
    use Locatable;
    
    protected $guarded = [];

    protected static function boot() 
    {
        parent::boot();
        
        static::deleting(function ($model) {
            $model->postable->delete();
            $model->accompaniments->each->delete();
            $model->reactions->each->delete();
        });
    }
    
    /**
     * Fetch the associated subject for the activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function postable() 
    {
        return $this->morphTo();
    }


    /**
     * Fetch the associated accompaniments for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accompaniments() 
    {
        return $this->hasMany(Accompaniment::class);
    }

    /**
     * Fetch the associated reactions for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions() 
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Fetch the associated comments for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments() 
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Attach accompaniments to a post.
     *
     * @param  $accompaniments
     */
    public function addAccompaniments($accompaniments) 
    {
      return $this->accompaniments()->createMany($accompaniments);
    }

    /**
     * Attach accompaniments to a post.
     *
     * @param  $reaction
     */
    public function addReaction($reaction)
    {
        return $this->reactions()->create($reaction);
    }

    /**
     * Attach accompaniments to a post.
     *
     * @param  $reaction
     */
    public function addComment($comment)
    {
        return $this->comments()->create($comment);
    }

    /**
     * Fetch an activity feed for the given user.
     *
     * @param  User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function feed($user) 
    {
        $ids = $user->getFriends()->map->id->prepend($user->id);
        
        return static::with('postable')
            ->latest()
            ->whereIn('user_id', $ids)
            ->get();
            
    }
    
}
