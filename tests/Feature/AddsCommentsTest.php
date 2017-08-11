<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Notification;
use Tests\TestCase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddsCommentsTest extends TestCase
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
    function a_post_author_is_notified_when_a_user_comments_on_their_post()
    {
        $author = $this->post->user;
        $this->createFriendship(auth()->user(), $author);

        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/comments', ['body' => 'This is a comment']);
        $this->assertCount(1, $author->notifications);
    }

    /** @test */
    function a_user_cannot_see_comments_on_a_post_that_does_not_belong_to_a_friend()
    {
        $this->withExceptionHandling();

        $response = $this->json('GET', 'api/posts/'.$this->post->id.'/comments');

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_see_comments_on_a_post_that_belongs_to_a_friend()
    {
        $this->createFriendship(auth()->user(), $this->user);
        $response = $this->json('GET', 'api/posts/'.$this->post->id.'/comments');

        $response->assertStatus(200);
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
        $this->createFriendship(auth()->user(), $this->user);
        $response = $this->json('POST', 'api/posts/'.$this->post->id.'/comments', ['body' => 'This is a comment']);

        $response->assertStatus(200);

        $this->assertCount(1, $this->user->notifications);
        $this->assertDatabaseHas('comments', ['post_id' => $this->post->id]);
    }

    /** @test */
    function a_user_cannot_update_a_comment_that_does_not_belong_to_them()
    {
        $this->withExceptionHandling();
        $comment = create('Knot\Models\Comment');
        $response = $this->json('PUT', 'api/comments/'.$comment->id, ['body' => 'Updating comment']);

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_update_their_own_comments()
    {
        $comment = create('Knot\Models\Comment', ['user_id' => auth()->id()]);
        $response = $this->json('PUT', 'api/comments/'.$comment->id, ['body' => 'Updating comment']);

        $response->assertStatus(200);
    }

    /** @test */
    function a_user_cannot_delete_a_comment_that_does_not_belong_to_them()
    {
        $this->withExceptionHandling();
        $comment = create('Knot\Models\Comment');
        $response = $this->json('DELETE', 'api/comments/'.$comment->id);

        $response->assertStatus(403);
    }

    /** @test */
    function a_user_can_delete_their_own_comments()
    {
        $comment = create('Knot\Models\Comment', ['user_id' => auth()->id()]);
        $response = $this->json('DELETE', '/api/comments/'.$comment->id);

        $response->assertStatus(200);
    }

    /** @test */
    function participants_in_a_comments_thread_are_notified_except_for_the_post_author_and_comment_author()
    {
        $author = $this->post->user;
        $this->createFriendship(auth()->user(), $author);
        create('Knot\Models\Comment', ['post_id' => $this->post->id], 4);
        $comment = $this->json('POST', 'api/posts/'.$this->post->id.'/comments', ['body' => 'This is a comment'])->getOriginalContent();
        $this->assertCount(1, $author->notifications);
        // 4 commenters + 1 existing author notification
        $this->assertCount(5, DatabaseNotification::all());
    }
}
