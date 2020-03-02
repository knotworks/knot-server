<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Knot\Traits\Locatable;

class Post extends Model
{
    use Locatable;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->accompaniments->each->delete();
            $model->reactions->each->delete();
            $model->comments->each->delete();
        });
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }

    /**
     * Fetch the associated user for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
