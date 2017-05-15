<?php

namespace FamJam\Models;

use Illuminate\Database\Eloquent\Model;
use FamJam\Traits\Postable;

class PhotoPost extends Model
{
    use Postable;

    protected $table = 'photo_posts';
    protected $with = ['post'];

    protected $fillable = ['user_id', 'body', 'image_url'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function fullImageUrl()
    {
        $bucket = config('filesystems.disks.b2.bucketName');
        return "https://f001.backblazeb2.com/file/{$bucket}/{$this->image_url}";
    }
}
