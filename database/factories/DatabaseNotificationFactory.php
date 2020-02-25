<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function () {
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
