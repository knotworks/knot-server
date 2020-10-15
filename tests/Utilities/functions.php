<?php

use Illuminate\Support\Collection;

function create($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->make($attributes);
}

function raw($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->raw($attributes);
}

Collection::macro('hasKeys', function ($keys) {
    return ! array_diff_key(array_flip($keys), $this->toArray());
});
