<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Knot\Traits\Postable;

class PhotoPost extends Model
{
    use Postable;

    protected $table = 'photo_posts';

    protected $fillable = [
        'user_id',
        'body',
        'image_path',
        'width',
        'height',
        'cloud',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
