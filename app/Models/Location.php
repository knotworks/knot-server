<?php

namespace Knot\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    /**
     * Fetch the associated subject for the location.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function locatable() 
    {
        return $this->morphTo();
    }
}
