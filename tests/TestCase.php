<?php

namespace Tests;

use Laravel\Airlock\Airlock;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function authenticate($user = null)
    {
        $user = $user ?: create(\Knot\Models\User::class);
        Airlock::actingAs($user, ['*']);

        return $this;
    }

    protected function createFriendship($user1, $user2)
    {
        $user1->befriend($user2);
        $user2->acceptFriendRequest($user1);

        return $this;
    }

}
