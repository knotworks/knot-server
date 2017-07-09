<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    function a_user_can_fetch_their_notifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson("/api/notifications")->json()
        );
    }

    /** @test */
    function a_user_can_mark_their_notifications_as_reads()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1, auth()->user()->unreadNotifications);

        $this->deleteJson("/api/notifications");

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
