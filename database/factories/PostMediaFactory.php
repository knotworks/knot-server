<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Knot\Models\PostMedia;

$factory->define(PostMedia::class, function (Faker $faker) {
    return [
        'post_id' => factory(\Knot\Models\Post::class),
        'path' => 'media/'.$faker->md5(),
        'width' => 1200,
        'height' => 800,
        'type' => "image",
    ];
});
