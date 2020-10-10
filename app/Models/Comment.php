<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Knot\Traits\Locatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use Locatable, HasFactory;

    protected $guarded = [];
    protected $with = ['user', 'location'];

    /**
     * Fetch the associated post for the reaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Fetch the associated user for the reaction.
     *
     * @return \Knot\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
