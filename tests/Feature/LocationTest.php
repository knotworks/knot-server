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
        $this->authenticate();
    }

    /** @test */
    public function a_user_can_include_a_location_with_a_post()
    {
        $this->withoutExceptionHandling();
        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'location' => [
                'name' => $this->faker->company,
                'lat' => $this->faker->latitude,
                'long' => $this->faker->longitude,
            ],
        ];

        $this->postJson('api/posts', $postContent)
            ->assertStatus(201)
            ->assertJson([
                'body' => $postContent['body'],
                'location' => $postContent['location'],
            ]);
    }

    /** @test */
    public function a_location_must_have_a_valid_latitude_and_longitude()
    {
        $this->withExceptionHandling();

        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'location' => [
                'name' => $this->faker->company,
                'lat' => 'cdsfew',
                'long' => '3jdjdsjasd',
            ],
        ];

        $response = $this->postJson('api/posts', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('location.lat', $response->getOriginalContent()['errors']));
        $this->assertTrue(array_key_exists('location.long', $response->getOriginalContent()['errors']));
    }
}
