<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Knot\Traits\Postable;

class TextPost extends Model
{
    use Postable;

    protected $table = 'text_posts';

    protected $fillable = ['user_id', 'body'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

