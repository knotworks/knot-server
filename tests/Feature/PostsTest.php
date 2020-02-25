<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_user_can_view_their_own_post()
    {
        $post = create('Knot\Models\Post', ['user_id' => auth()->id()]);

        $this->getJson('/api/posts/'.$post->id)->assertStatus(200);
    }

    /** @test */
    public function a_user_can_view_their_friends_post()
    {
        $user = create('Knot\Models\User');
        $this->createFriendship(auth()->user(), $user);

        $post = create('Knot\Models\Post', ['user_id' => $user->id]);

        $this->getJson('/api/posts/'.$post->id)->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_view_non_friends_post()
    {
        $user = create('Knot\Models\User');
        $post = create('Knot\Models\Post', ['user_id' => $user->id]);

        $this->getJson('/api/posts/'.$post->id)->assertStatus(403);
    }

    /** @test */
    public function a_user_can_delete_their_own_post()
    {
        $this->withoutExceptionHandling();
        $post = create('Knot\Models\Post', ['user_id' => auth()->id()]);

        $this->deleteJson('/api/posts/'.$post->id)->assertStatus(204);
        $this->assertEquals(0, \Knot\Models\Post::count());
    }

    /** @test */
    public function a_user_cannot_delete_someone_elses_post()
    {
        $user = create('Knot\Models\User');
        $post = create('Knot\Models\Post', ['user_id' => $user->id]);

        $this->deleteJson('/api/posts/'.$post->id)->assertStatus(403);
    }
}
