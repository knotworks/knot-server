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
    function the_authenticated_user_can_fetch_their_friendships()
    {
      $sender = create('FamJam\Models\User');
      $sender->befriend(auth()->user());

      $response = $this->json('GET', 'api/friendships');

      $response->assertStatus(200);
      $this->assertCount(1, $response->getOriginalContent());
    }

    /** @test */
    function the_authenticated_user_can_accept_a_friend_request()
    {
      $sender = create('FamJam\Models\User');
      $sender->befriend(auth()->user());

      $response = $this->json('POST', 'api/friendships/accept/'.$sender->id);

      $response->assertStatus(200);
      $this->assertTrue(auth()->user()->isFriendWith($sender));
    }

    /** @test */
    function the_authenticated_user_can_deny_a_friend_request()
    {
      $sender = create('FamJam\Models\User');
      $sender->befriend(auth()->user());

      $response = $this->json('POST', 'api/friendships/deny/'.$sender->id);

      $response->assertStatus(200);
      $this->assertFalse(auth()->user()->isFriendWith($sender));
    }
}