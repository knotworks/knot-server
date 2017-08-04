<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class HandlesFriendshipsTest extends TestCase
{
    use DatabaseMigrations;
    
    public function setup()
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    function a_user_can_fetch_their_friendships()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());

        $response = $this->json('GET', 'api/friendships');

        $response->assertStatus(200);
        $this->assertCount(1, $response->getOriginalContent());
    }

    /** @test */
    function a_user_can_add_a_new_friend()
    {
        $recipient = create('Knot\Models\User');
        $response = $this->postJson('api/friendships/add/'.$recipient->id);
        $this->assertTrue($recipient->hasFriendRequestFrom(auth()->user()));
        $this->assertCount(1, $recipient->notifications);
    }

    /** @test */
    function a_user_can_accept_a_friend_request()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());

        $response = $this->json('POST', 'api/friendships/accept/'.$sender->id);

        $response->assertStatus(200);
        $this->assertTrue(auth()->user()->isFriendWith($sender));
    }

    /** @test */
    function a_user_can_deny_a_friend_request()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());

        $response = $this->json('POST', 'api/friendships/deny/'.$sender->id);

        $response->assertStatus(200);
        $this->assertFalse(auth()->user()->isFriendWith($sender));
    }

    /** @test */
    function a_user_can_unfriend_a_friend()
    {
        $sender = create('Knot\Models\User');
        $sender->befriend(auth()->user());
        auth()->user()->acceptFriendRequest($sender);
        $this->assertTrue(auth()->user()->isFriendWith($sender));

        $response = $this->json('POST', 'api/friendships/unfriend/'.$sender->id);

        $response->assertStatus(200);
        $this->assertFalse(auth()->user()->isFriendWith($sender));
    }
}
