<?php

namespace FamJam\Traits;

trait Locatable {

    protected static function bootLocatable() {
        static::deleting(function ($model) {
            $model->location->delete();
        });
    }
    
    public function location() {
        return $this->morphOne('FamJam\Models\Location', 'locatable');
    }

    public function addLocation($location) {
        $this->location()->create($location);
    }
}