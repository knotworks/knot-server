<?php

use Illuminate\Support\Str;
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
$factory->define(Knot\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => Str::random(10),
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Knot\Models\TextPost::class, function (Faker\Generator $faker) {
    return [
        'body' => $faker->sentence,
        'user_id' => function () {
            return factory(\Knot\Models\User::class)->create()->id;
        },
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Knot\Models\Location::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(\Knot\Models\User::class)->create()->id;
        },
        'locatable_id' => function () {
            return factory(\Knot\Models\TextPost::class)->create()->post->id;
        },
        'locatable_type' => \Knot\Models\Post::class,
        'lat' => $faker->latitude,
        'long' => $faker->longitude,
        'city' => $faker->city,
        'name' => $faker->company,
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Knot\Models\Reaction::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(\Knot\Models\User::class)->create()->id;
        },
        'post_id' => function () {
            return factory(\Knot\Models\TextPost::class)->create()->post->id;
        },
        'type' => Knot\Models\Reaction::REACTIONS[array_rand(Knot\Models\Reaction::REACTIONS)],
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Knot\Models\Accompaniment::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(\Knot\Models\User::class)->create()->id;
        },
        'post_id' => function () {
            return factory(\Knot\Models\TextPost::class)->create()->post->id;
        },
        'name' => $faker->name,
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Knot\Models\Comment::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(\Knot\Models\User::class)->create()->id;
        },
        'post_id' => function () {
            return factory(\Knot\Models\TextPost::class)->create()->post->id;
        },
        'body' => $faker->sentence,
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function ($faker) {
    return [
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => \Knot\Notifications\PostCommentedOn::class,
        'notifiable_id' => function () {
            return auth()->id() ?: factory(\Knot\Models\User::class)->create()->id;
        },
        'notifiable_type' => \Knot\Models\User::class,
        'data' => ['foo' => 'bar'],
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Knot\Models\PhotoPost::class, function (Faker\Generator $faker) {
    $hash = $faker->md5;

    return [
        'body' => $faker->sentence,
        'user_id' => function () {
            return factory(\Knot\Models\User::class)->create()->id;
        },
        'image_path' => "photo-posts/{$hash}.jpg",
        'width' => 1200,
        'height' => 800,
        'cloud' => false,
    ];
});
