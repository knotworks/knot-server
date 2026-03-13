<?php

declare(strict_types=1);

use App\Notifications\PostReactedTo;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
    $this->user = create('App\Models\User');
    $this->post = create('App\Models\Post', ['user_id' => $this->user->id]);
    $this->login();
});

it('prevents a user from reacting to a post that does not belong to a friend', function (): void {
    $this->postJson('api/posts/' . $this->post->id . '/reactions', ['type' => 'smile'])->assertStatus(403);
});

it('allows a user to react to a friends post', function (): void {
    $this->createFriendship(auth()->user(), $this->user);

    $this->postJson('api/posts/' . $this->post->id . '/reactions', ['type' => 'smile'])->assertStatus(200);

    $this->assertDatabaseHas('reactions', ['post_id' => $this->post->id]);
});

it('requires the reaction type to be one of the supported reactions', function (): void {
    $this->createFriendship(auth()->user(), $this->user);

    $this->postJson('api/posts/' . $this->post->id . '/reactions', ['type' => 'cringe'])->assertStatus(422);
});

it('notifies a post author when someone reacts to their post', function (): void {
    $this->createFriendship(auth()->user(), $this->user);
    $this->postJson('api/posts/' . $this->post->id . '/reactions', ['type' => 'smile']);

    Notification::assertSentTo($this->user, PostReactedTo::class);
});

it('does not notify the post author when they react to their own post', function (): void {
    $post = create('App\Models\Post', ['user_id' => auth()->id()]);
    $this->postJson('api/posts/' . $post->id . '/reactions', ['type' => 'smile']);

    Notification::assertNotSentTo($this->user, PostReactedTo::class);
});
