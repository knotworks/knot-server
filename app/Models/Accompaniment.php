<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accompaniment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

    /**
     * Fetch the associated post for the accompaniment.
     *
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Fetch the associated user for the accompaniment.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
