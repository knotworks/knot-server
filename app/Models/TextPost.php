<?php

namespace Knot\Models;

use Knot\Traits\Postable;
use Illuminate\Database\Eloquent\Model;

class TextPost extends Model
{
    use Postable;

    protected $table = 'text_posts';

    protected $fillable = ['user_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
