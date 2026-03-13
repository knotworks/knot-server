<?php

declare(strict_types=1);

use App\Notifications\CommentRepliedTo;
use App\Notifications\PostCommentedOn;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
    $this->user = create('App\Models\User');
    $this->post = create('App\Models\Post', ['user_id' => $this->user->id]);
    $this->login();
});

it('prevents a user from seeing comments on a post that does not belong to a friend', function (): void {
    $this->getJson('api/posts/' . $this->post->id . '/comments')->assertStatus(403);
});

it('allows a user to see comments on a post that belongs to a friend', function (): void {
    $this->createFriendship(auth()->user(), $this->user);

    $this->getJson('api/posts/' . $this->post->id . '/comments')->assertStatus(200);
});

it('prevents a user from commenting on a post that does not belong to a friend', function (): void {
    $this->withExceptionHandling();

    $this->postJson('api/posts/' . $this->post->id . '/comments', ['body' => 'This is a comment'])->assertStatus(403);
});

it('allows a user to comment on a post that belongs to a friend', function (): void {
    $this->createFriendship(auth()->user(), $this->user);

    $this->postJson('api/posts/' . $this->post->id . '/comments', ['body' => 'This is a comment'])->assertStatus(201);

    $this->assertDatabaseHas('comments', ['post_id' => $this->post->id]);
});

it('prevents a user from updating a comment that does not belong to them', function (): void {
    $this->withExceptionHandling();

    $comment = create('App\Models\Comment');

    $this->putJson('api/comments/' . $comment->id, ['body' => 'Updating comment'])->assertStatus(403);
});

it('allows a user to update their own comments', function (): void {
    $comment = create('App\Models\Comment', ['user_id' => auth()->id()]);

    $this->putJson('api/comments/' . $comment->id, ['body' => 'Updating comment'])->assertStatus(200);
});

it('prevents a user from deleting a comment that does not belong to them', function (): void {
    $comment = create('App\Models\Comment');

    $this->deleteJson('api/comments/' . $comment->id)->assertStatus(403);
});

it('allows a user to delete their own comments', function (): void {
    $comment = create('App\Models\Comment', ['user_id' => auth()->id()]);

    $this->deleteJson('/api/comments/' . $comment->id)->assertStatus(204);
});

it('notifies the post author when a user comments on their post', function (): void {
    $author = $this->post->user;
    $this->createFriendship(auth()->user(), $author);

    $this->postJson('api/posts/' . $this->post->id . '/comments', ['body' => 'This is a comment']);

    Notification::assertSentTo($author, PostCommentedOn::class);
});

it('notifies thread participants except for the post author and comment author', function (): void {
    $author = $this->post->user;
    $this->createFriendship(auth()->user(), $author);
    create('App\Models\Comment', ['post_id' => $this->post->id], 4);

    $this->postJson('api/posts/' . $this->post->id . '/comments', ['body' => 'This is a comment']);

    $commenters = $this->post->comments->slice(0, -1)->map(function ($comment) {
        return $comment->user;
    });

    Notification::assertSentTo($commenters, CommentRepliedTo::class);
    Notification::assertNotSentTo($author, CommentRepliedTo::class);
    Notification::assertSentTo($author, PostCommentedOn::class);
});
