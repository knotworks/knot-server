<?php

declare(strict_types=1);

use Faker\Factory as Faker;

beforeEach(function (): void {
    $this->faker = Faker::create();
    $this->login();
});

it('allows a user to include a location with a post', function (): void {
    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'location' => [
            'name' => $this->faker->company,
            'lat' => $this->faker->latitude . '',
            'lon' => $this->faker->longitude . '',
        ],
    ];

    $this->postJson('api/posts', $postContent)
        ->assertStatus(201)
        ->assertJson([
            'body' => $postContent['body'],
            'location' => [
                'name' => $postContent['location']['name'],
                'lat' => (string) $postContent['location']['lat'],
                'long' => (string) $postContent['location']['lon'],
            ],
        ]);
});

it('requires a location to have a valid latitude and longitude', function (): void {
    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'location' => [
            'name' => $this->faker->company,
            'lat' => 'cdsfew',
            'lon' => '3jdjdsjasd',
        ],
    ];

    $this->postJson('api/posts', $postContent)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['location.lat', 'location.lon']);
});
