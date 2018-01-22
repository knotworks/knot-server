<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Knot\Traits\Postable;
use Illuminate\Support\Facades\Storage;

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

    protected $hidden = ['image_path'];

    protected $appends = ['image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute()
    {
        $imagePath = $this->image_path;
        if ($this->cloud) {
            return Storage::cloud()->url($imagePath);
        } else {
            return asset('images/tmp/photo-posts/' . pathinfo($imagePath, PATHINFO_BASENAME));
        }
    }
}
