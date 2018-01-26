<?php

namespace Tests\Feature;

use Tests\TestCase;
use Knot\Models\TextPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function setup()
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_user_can_view_their_own_post()
    {
        $post = create('Knot\Models\TextPost', ['user_id' => auth()->id()])->post;

        $this->getJson('/api/posts/' . $post->id)->assertStatus(200);
    }

    /** @test */
    public function a_user_can_view_their_friends_post()
    {
        $user = create('Knot\Models\User');
        $this->createFriendship(auth()->user(), $user);

        $post = create('Knot\Models\TextPost', ['user_id' => $user->id])->post;

        $this->getJson('/api/posts/' . $post->id)->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_view_non_friends_post()
    {
        $this->withExceptionHandling();

        $user = create('Knot\Models\User');
        $post = create('Knot\Models\TextPost', ['user_id' => $user->id])->post;

        $this->getJson('/api/posts/' . $post->id)->assertStatus(403);
    }

    /** @test */
    public function a_user_can_delete_their_own_post()
    {
        $post = create('Knot\Models\TextPost', ['user_id' => auth()->id()])->post;

        $this->deleteJson('/api/posts/' . $post->id)->assertStatus(204);
        $this->assertEquals(0, TextPost::count());
    }

    /** @test */
    public function a_user_cannot_delete_someone_elses_post()
    {
        $this->withExceptionHandling();

        $user = create('Knot\Models\User');
        $post = create('Knot\Models\TextPost', ['user_id' => $user->id])->post;

        $this->deleteJson('/api/posts/' . $post->id)->assertStatus(403);
    }
}
