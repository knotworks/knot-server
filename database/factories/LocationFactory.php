<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Knot\Models\Location;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'locatable_id' => factory(\Knot\Models\Post::class),
        'locatable_type' => \Knot\Models\Post::class,
        'source' => 'foursquare',
        'lat' => $faker->latitude,
        'long' => $faker->longitude,
        'city' => $faker->city,
        'name' => $faker->company,
    ];
});
