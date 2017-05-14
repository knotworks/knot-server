<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddsCommentsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $post;
    
    public function setup()
    {
        parent::setup();

        $this->user = create('FamJam\Models\User');
        $this->post = create('FamJam\Models\TextPost', ['user_id' => $this->user->id])->post;
        $this->authenticate();
    }

    /** @test */
    function a_user_cannot_comment_on_a_post_that_does_not_belong_to_a_friend()
    {
        $this->withExceptionHandling();
        
        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/comments', ['body' => 'This is a comment']);

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_comment_on_a_post_if_it_does_belong_to_a_friend()
    {
        $this->createMutualFriendship();
        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/comments', ['body' => 'This is a comment']);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('comments', ['post_id' => $this->post->id]);
    }

    /** @test */
    function a_user_cannot_update_a_comment_that_does_not_belong_to_them()
    {
        $this->withExceptionHandling();
        $comment = create('FamJam\Models\Comment');
        $response = $this->json('PUT', 'api/comments/'.$comment->id, ['body' => 'Updating comment']);

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_update_their_own_comments()
    {
        $comment = create('FamJam\Models\Comment', ['user_id' => auth()->id()]);
        $response = $this->json('PUT', 'api/comments/'.$comment->id, ['body' => 'Updating comment']);

        $response->assertStatus(200);
    }

    /** @test */
    function a_user_cannot_delete_a_comment_that_does_not_belong_to_them()
    {
        $this->withExceptionHandling();
        $comment = create('FamJam\Models\Comment');
        $response = $this->json('DELETE', 'api/comments/'.$comment->id);

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_delete_their_own_comments()
    {
        $comment = create('FamJam\Models\Comment', ['user_id' => auth()->id()]);
        $response = $this->json('DELETE', '/api/comments/'.$comment->id);

        $response->assertStatus(200);
    }


    protected function createMutualFriendship()
    {
        auth()->user()->befriend($this->user);
        $this->user->acceptFriendRequest(auth()->user());
    }
    
}