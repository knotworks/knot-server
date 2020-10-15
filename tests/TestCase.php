<?php

namespace Tests;

use Knot\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp() : void
    {
        parent::setUp();
    }

    protected function login($user = null)
    {
        $user = $user ?: create(User::class);
        Sanctum::actingAs($user, ["*"], 'web');

        return $this;
    }

    protected function createFriendship($user1, $user2)
    {
        $user1->befriend($user2);
        $user2->acceptFriendRequest($user1);

        return $this;
    }
}
