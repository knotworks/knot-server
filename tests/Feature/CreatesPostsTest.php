<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Faker\Factory as Faker;

class CreatesTextPostTest extends TestCase
{
    use DatabaseMigrations;

    protected $faker;

    public function setup()
    {
        parent::setup();
        $this->faker = Faker::create();
    }
    /** @test */
    function a_user_can_create_a_text_post()
    {
        $this->authenticate();

        $postContent =  ['body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.'];
        $response = $this->json('POST', 'api/posts/new/text', $postContent);

        $response
        ->assertStatus(200)
        ->assertJson($postContent);
    }

    /** @test */
    function a_user_can_include_a_location_with_a_post()
    {
        $this->authenticate();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'location' => [
                'name' => $this->faker->company,
                'lat' => $this->faker->latitude,
                'long' => $this->faker->longitude,
            ],
        ];

        $response = $this->json('POST', 'api/posts/new/text', $postContent);

        $response
        ->assertStatus(200)
        ->assertJson([
            'body' => $postContent['body'],
            'post' => [
                'location' => $postContent['location']
            ],
        ]);
    }

    /** @test */
    function a_user_can_include_accompaniments_with_a_post()
    {
        $this->authenticate();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => null, 'name' => 'Jane Doe'],
                ['user_id' => 2, 'name' => 'John Doe'],
            ],
        ];

        $response = $this->json('POST', 'api/posts/new/text', $postContent);

        $response
        ->assertStatus(200)
        ->assertJson([
            'body' => $postContent['body'],
            'post' => [
                'accompaniments' => $postContent['accompaniments'],
            ],
        ]);
    }

}
