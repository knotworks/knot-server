<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Knot\Models\Comment;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id' => factory(\Knot\Models\User::class),
        'post_id' => factory(\Knot\Models\Post::class),
        'body' => $faker->sentence,
    ];
});
