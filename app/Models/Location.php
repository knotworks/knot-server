<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Fetch the associated subject for the location.
     *
     * @return MorphTo
     */
    public function locatable()
    {
        return $this->morphTo();
    }
}
