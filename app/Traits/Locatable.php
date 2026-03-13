<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Location;

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
        return $this->morphOne(Location::class, 'locatable');
    }

    public function addLocation($location)
    {
        $this->location()->create($location);
    }
}
