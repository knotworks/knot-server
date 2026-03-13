<?php

declare(strict_types=1);

use App\Models\Post;

beforeEach(function (): void {
    $this->login();
});

it('allows a user to view their own post', function (): void {
    $post = create('App\Models\Post', ['user_id' => auth()->id()]);

    $this->getJson('/api/posts/' . $post->id)->assertStatus(200);
});

it('allows a user to view their friends post', function (): void {
    $user = create('App\Models\User');
    $this->createFriendship(auth()->user(), $user);

    $post = create('App\Models\Post', ['user_id' => $user->id]);

    $this->getJson('/api/posts/' . $post->id)->assertStatus(200);
});

it('prevents a user from viewing a non-friends post', function (): void {
    $user = create('App\Models\User');
    $post = create('App\Models\Post', ['user_id' => $user->id]);

    $this->getJson('/api/posts/' . $post->id)->assertStatus(403);
});

it('allows a user to delete their own post', function (): void {
    $this->withoutExceptionHandling();

    $post = create('App\Models\Post', ['user_id' => auth()->id()]);

    $this->deleteJson('/api/posts/' . $post->id)->assertStatus(204);

    expect(Post::count())->toBe(0);
});

it('prevents a user from deleting someone elses post', function (): void {
    $user = create('App\Models\User');
    $post = create('App\Models\Post', ['user_id' => $user->id]);

    $this->deleteJson('/api/posts/' . $post->id)->assertStatus(403);
});
