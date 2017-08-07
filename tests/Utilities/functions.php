<?php

use Illuminate\Support\Collection;

function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

Collection::macro('hasKeys', function ($keys) {
    return !array_diff_key(array_flip($keys), $this->toArray());
});
