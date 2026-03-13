<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Locatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory, Locatable;

    protected $guarded = [];

    protected $with = ['user', 'location'];

    /**
     * Fetch the associated post for the reaction.
     *
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Fetch the associated user for the reaction.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
