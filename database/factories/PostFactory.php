<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Knot\Models\Post;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' => factory(\Knot\Models\User::class),
        'body' => $faker->sentence(),
    ];
});
