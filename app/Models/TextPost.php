<?php

namespace FamJam\Models;

use Illuminate\Database\Eloquent\Model;
use FamJam\Traits\Postable;

class TextPost extends Model
{
    use Postable;

    protected $table = 'text_posts';
    protected $with = ['post'];

    protected $fillable = ['user_id', 'body'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

