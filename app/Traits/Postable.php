<?php

namespace Knot\Traits;

trait Postable
{
    protected static function bootPostable()
    {
        static::created(function ($model) {
            $model->createPost($model);
        });

        static::deleting(function ($model) {
            $model->post()->delete();
        });
    }

    public function post()
    {
        return $this->morphOne(\Knot\Models\Post::class, 'postable');
    }

    protected function createPost($model)
    {
        $this->post()->create(['user_id' => $model->user_id]);
    }
}
