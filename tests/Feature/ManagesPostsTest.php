<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Tests\TestCase;
use Knot\Models\TextPost;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManagesPostsTest extends TestCase
{
    use DatabaseMigrations;

    protected $faker;

    public function setup()
    {
        parent::setup();
        $this->faker = Faker::create();

        $this->authenticate();
    }

    /** @test */
    function a_user_can_view_their_own_post()
    {
        $post = create('Knot\Models\TextPost', ['user_id' => auth()->id()])->post;

        $this->getJson('/api/posts/'.$post->id)->assertStatus(200);
    }

    /** @test */
    function a_user_can_view_their_friends_post()
    {
        $user = create('Knot\Models\User');
        $this->createFriendship(auth()->user(), $user);

        $post = create('Knot\Models\TextPost', ['user_id' => $user->id])->post;

        $this->getJson('/api/posts/'.$post->id)->assertStatus(200);
    }

    /** @test */
    function a_user_cannot_view_non_friends_post()
    {
        $this->withExceptionHandling();

        $user = create('Knot\Models\User');
        $post = create('Knot\Models\TextPost', ['user_id' => $user->id])->post;

        $this->getJson('/api/posts/'.$post->id)->assertStatus(403);
    }

    /** @test */
    function a_user_can_delete_their_own_post()
    {
        $post = create('Knot\Models\TextPost', ['user_id' => auth()->id()])->post;
        $this->deleteJson('/api/posts/'.$post->id)->assertStatus(204);
        $this->assertEquals(0, TextPost::count());

    }

    /** @test */
    function a_user_cannot_delete_someone_elses_post()
    {
        $this->withExceptionHandling();

        $user = create('Knot\Models\User');
        $post = create('Knot\Models\TextPost', ['user_id' => $user->id])->post;

        $this->deleteJson('/api/posts/'.$post->id)->assertStatus(403);
    }

    /** @test */
    function a_user_can_include_a_location_with_a_post()
    {
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
