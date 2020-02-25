<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Knot\Models\Accompaniment;

$factory->define(Accompaniment::class, function (Faker $faker) {
    return [
        'user_id' => factory(\Knot\Models\User::class),
        'post_id' => factory(\Knot\Models\Post::class),
        'name' => $faker->name,
    ];
});
