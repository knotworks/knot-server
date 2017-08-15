<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ViewsPostTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
        parent::setup();

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
}
