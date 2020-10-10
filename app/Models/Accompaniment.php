<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accompaniment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

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
        return $this->belongsTo(User::class);
    }
}
