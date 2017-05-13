<?php

namespace FamJam\models;

use Illuminate\Database\Eloquent\Model;
use FamJam\Traits\Locatable;

class Comment extends Model
{
    use Locatable;
    
    protected $guarded = [];

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
     * @return \FamJam\Models\User
     */
    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
