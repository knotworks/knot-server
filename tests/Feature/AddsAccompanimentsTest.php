<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddsAccompanimentsTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
        parent::setup();

        $this->authenticate();

    }

    /** @test */
    function a_user_can_include_accompaniments_with_a_post()
    {
        $this->withExceptionHandling();

        $user = create('Knot\Models\User');
        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => null, 'name' => 'Jane Doe'],
                ['user_id' => $user->id, 'name' => $user->full_name],
            ],
        ];
        $this->postJson('api/posts/new/text', $postContent)
        ->assertStatus(200)
        ->assertJson([
            'body' => $postContent['body'],
            'post' => [
                'accompaniments' => $postContent['accompaniments'],
            ],
        ]);
    }

    /** @test */
    function all_accompaniments_require_a_name()
    {
        $this->withExceptionHandling();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => null, 'name' => ''],
            ],
        ];
        $response = $this->postJson('api/posts/new/text', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('accompaniments.0.name', $response->getOriginalContent()));
    }

    /** @test */
    function all_accompaniments_names_should_be_strings()
    {
        $this->withExceptionHandling();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => null, 'name' => 13],
            ],
        ];
        $response = $this->postJson('api/posts/new/text', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('accompaniments.0.name', $response->getOriginalContent()));
    }

    /** @test */
    function accompaniments_ids_must_be_numeric()
    {
        $this->withExceptionHandling();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => 'butts', 'name' => 'Jane Doe'],
            ],
        ];
        $response = $this->postJson('api/posts/new/text', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('accompaniments.0.user_id', $response->getOriginalContent()));
    }

    /** @test */
    function accompaniments_ids_must_be_distinct()
    {
        $this->withExceptionHandling();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => 1, 'name' => 'Jane Doe'],
                ['user_id' => 1, 'name' => 'John Doe'],
            ],
        ];
        $response = $this->postJson('api/posts/new/text', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('accompaniments.0.user_id', $response->getOriginalContent()));
    }

    /** @test */
    function accompaniments_ids_must_match_a_user_in_the_database()
    {
        $this->withExceptionHandling();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => 7, 'name' => 'Jane Doe'],
            ],
        ];
        $response = $this->postJson('api/posts/new/text', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('accompaniments.0.user_id', $response->getOriginalContent()));
    }

    /** @test */
    function accompaniments_ids_must_not_include_the_authenticated_users_id()
    {
        $this->withExceptionHandling();

        $postContent =  [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => auth()->id(), 'name' => 'Jane Doe'],
            ],
        ];
        $response = $this->postJson('api/posts/new/text', $postContent)->assertStatus(422);
        $this->assertTrue(array_key_exists('accompaniments.0.user_id', $response->getOriginalContent()));
    }
}
