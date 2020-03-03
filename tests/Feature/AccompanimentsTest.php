<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccompanimentsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_user_can_include_accompaniments_with_a_post()
    {
        $users = create('Knot\Models\User', [], 3);

        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => $users->map->id,
        ];

        $this->postJson('api/posts', $postContent)
        ->assertStatus(201)
        ->assertJson([
            'body' => $postContent['body'],
            'accompaniments' => $users->map(function ($user) {
                return ['user_id' => ''.$user['id']];
            })->toArray(),
        ]);
    }

    /** @test */
    public function accompaniments_ids_must_be_numeric()
    {
        $users = create('Knot\Models\User', [], 3);
        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => ['nope', 'noooo'],
        ];
        $this->postJson('api/posts', $postContent)->assertStatus(422);
    }

    /** @test */
    public function accompaniments_ids_must_be_distinct()
    {
        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => 1],
                ['user_id' => 1],
            ],
        ];
        $this->postJson('api/posts', $postContent)->assertStatus(422);
    }

    /** @test */
    public function accompaniments_ids_must_match_a_user_in_the_database()
    {
        $users = create('Knot\Models\User', [], 1);
        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => 7],
            ],
        ];
        $this->postJson('api/posts', $postContent)->assertStatus(422);
    }

    /** @test */
    public function accompaniments_ids_must_not_include_the_authenticated_users_id()
    {
        $postContent = [
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'accompaniments' => [
                ['user_id' => auth()->id()],
            ],
        ];
        $this->postJson('api/posts', $postContent)->assertStatus(422);
    }
}
