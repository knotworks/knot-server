<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddsReactionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $post;

    public function setup()
    {
        parent::setup();

        $this->user = create('Knot\Models\User');
        $this->post = create('Knot\Models\TextPost', ['user_id' => $this->user->id])->post;
        $this->authenticate();
    }

    /** @test */
    function a_user_cannot_react_to_a_post_that_does_not_belong_to_a_friend()
    {
        $this->withExceptionHandling();

        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/reactions', ['type' => 'smile']);

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_react_to_a_post_if_it_does_belong_to_a_friend()
    {
        $this->createFriendship(auth()->user(), $this->user);
        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/reactions', ['type' => 'smile']);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reactions', ['post_id' => $this->post->id]);
    }

    /** @test */
    function a_reaction_type_must_be_one_of_the_set_reactions()
    {
        $this->withExceptionHandling();

        $this->createFriendship(auth()->user(), $this->user);
        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/reactions', ['type' => 'cringe']);

        $response->assertStatus(422);
    }
}
