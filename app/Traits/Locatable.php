<?php

namespace Knot\Traits;

trait Locatable
{
    protected static function bootLocatable()
    {
        static::deleting(function ($model) {
            if ($model->location()->exists()) {
                $model->location->delete();
            }
        });
    }

    public function location()
    {
        return $this->morphOne(\Knot\Models\Location::class, 'locatable');
    }

    public function addLocation($location)
    {
        $this->location()->create($location);
    }
}
