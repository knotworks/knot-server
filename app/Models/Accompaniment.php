<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;

class Accompaniment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'name'];

    /**
     * Fetch the associated post for the accompaniment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Fetch the associated user for the accompaniment.
     *
     * @return \Knot\Models\User
     */
    public function user()
    {
        if (!is_null($this->user_id)) {
            return User::find($this->user_id);
        } else {
            return null;
        }
    }
}
