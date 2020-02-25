<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Knot\Models\Reaction;

$factory->define(Reaction::class, function () {
    return [
        'user_id' => factory(\Knot\Models\User::class),
        'post_id' => factory(\Knot\Models\Post::class),
        'type' => Knot\Models\Reaction::REACTIONS[array_rand(Knot\Models\Reaction::REACTIONS)],
    ];
});
