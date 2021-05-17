<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected $faker;

    public function setup(): void
    {
        parent::setup();

        $this->faker = Faker::create();
        $this->login();
    }

    /** @test */
    public function a_user_can_include_a_location_with_a_post()
    {
        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'location' => [
                'name' => $this->faker->company,
                'lat' => $this->faker->latitude.'',
                'lon' => $this->faker->longitude.'',
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
    }

    /** @test */
    public function a_location_must_have_a_valid_latitude_and_longitude()
    {
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
    }
}
