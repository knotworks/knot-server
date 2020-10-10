<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'type'];

    const REACTIONS = [
        'smile' => 'smile',
        'love' => 'love',
        'frown' => 'frown',
        'surprise' => 'surprise',
        'laugh' => 'laugh',
        'angry' => 'angry',
    ];

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
