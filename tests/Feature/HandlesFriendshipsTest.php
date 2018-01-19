<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Tests\TestCase;
use Knot\Notifications\AddedAsFriend;
use Knot\Notifications\FriendRequestAccepted;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HandlesFriendshipsTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
        parent::setup();
        Notification::fake();
        $this->authenticate();
    }

    /** @test */
    function a_user_can_fetch_their_friendships()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());

        $response = $this->getJson('api/friendships')->assertStatus(200);
        $this->assertCount(1, $response->getOriginalContent()['requests']);
    }

    /** @test */
    function a_user_can_add_a_new_friend()
    {
        $recipient = create('Knot\Models\User');

        $response = $this->postJson('api/friendships/add/' . $recipient->id)->assertStatus(200);
        $this->assertTrue($recipient->hasFriendRequestFrom(auth()->user()));
        Notification::assertSentTo($recipient, AddedAsFriend::class);
    }

    /** @test */
    function a_user_can_accept_a_friend_request()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());

        $this->postJson('api/friendships/accept/' . $sender->id)->assertStatus(200);
        $this->assertTrue(auth()->user()->isFriendWith($sender));
        Notification::assertSentTo($sender, FriendRequestAccepted::class);
    }

    /** @test */
    function a_user_can_deny_a_friend_request()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());

        $this->postJson('api/friendships/deny/' . $sender->id)->assertStatus(200);
        $this->assertFalse(auth()->user()->isFriendWith($sender));
    }

    /** @test */
    function a_user_can_unfriend_a_friend()
    {
        $sender = create('Knot\Models\User');
        $this->createFriendship(auth()->user(), $sender);

        $this->assertTrue(auth()->user()->isFriendWith($sender));
        $this->postJson('api/friendships/unfriend/' . $sender->id)->assertStatus(200);
        $this->assertFalse(auth()->user()->isFriendWith($sender));
    }
}
