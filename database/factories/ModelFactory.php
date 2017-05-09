<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FamJam\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FamJam\Models\TextPost::class, function (Faker\Generator $faker) {
    return [
        'body' => $faker->sentence,
        'user_id' => function() {
            return factory('FamJam\Models\User')->create()->id;
        },
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FamJam\Models\Location::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function() {
            return factory('FamJam\Models\User')->create()->id;
        },
        'locatable_id' => function() {
            return factory('FamJam\Models\TextPost')->create()->postable->id;
        },
        'locatable_type' => 'FamJam\Models\Post',
        'lat' => $faker->latitude,
        'long' => $faker->longitude,
        'city' => $faker->city,
        'name' => $faker->company,
    ];
});
