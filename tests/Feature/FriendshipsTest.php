<?php

declare(strict_types=1);

use App\Notifications\AddedAsFriend;
use App\Notifications\FriendRequestAccepted;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
    $this->login();
});

it('allows a user to fetch their friendships', function (): void {
    $sender = create('App\Models\User');
    $sender->befriend(auth()->user());

    $response = $this->getJson('api/friendships')->assertStatus(200);

    expect($response->getOriginalContent()['requests'])->toHaveCount(1);
});

it('allows a user to add a new friend', function (): void {
    $recipient = create('App\Models\User');

    $this->postJson('api/friendships/add/' . $recipient->id)->assertStatus(200);

    expect($recipient->hasFriendRequestFrom(auth()->user()))->toBeTrue();
    Notification::assertSentTo($recipient, AddedAsFriend::class);
});

it('allows a user to accept a friend request', function (): void {
    $sender = create('App\Models\User');
    $sender->befriend(auth()->user());

    $this->postJson('api/friendships/accept/' . $sender->id)->assertStatus(200);

    expect(auth()->user()->isFriendWith($sender))->toBeTrue();
    Notification::assertSentTo($sender, FriendRequestAccepted::class);
});

it('allows a user to deny a friend request', function (): void {
    $sender = create('App\Models\User');
    $sender->befriend(auth()->user());

    $this->postJson('api/friendships/deny/' . $sender->id)->assertStatus(200);

    expect(auth()->user()->isFriendWith($sender))->toBeFalse();
});

it('allows a user to unfriend a friend', function (): void {
    $sender = create('App\Models\User');
    $this->createFriendship(auth()->user(), $sender);

    expect(auth()->user()->isFriendWith($sender))->toBeTrue();

    $this->postJson('api/friendships/unfriend/' . $sender->id)->assertStatus(200);

    expect(auth()->user()->isFriendWith($sender))->toBeFalse();
});
