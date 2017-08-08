<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;
use Knot\Traits\Postable;

class PhotoPost extends Model
{
    use Postable;

    protected $table = 'photo_posts';

    protected $fillable = ['user_id', 'body', 'image_path', 'width', 'height'];

    protected $appends = ['imageUrl'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute()
    {
        $path = config('filesystems.disks.b2.basePath');
        $bucket = config('filesystems.disks.b2.bucketName');
        $imagePath = $this->attributes['image_path'];
        if (starts_with($imagePath, 'photo-posts')) {
            return "{$path}{$bucket}/{$imagePath}";
        } else {
            return asset('images/tmp/'.pathinfo($imagePath, PATHINFO_BASENAME));
        }
    }
}
