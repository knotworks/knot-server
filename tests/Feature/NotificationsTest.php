<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    protected $post;

    public function setup()
    {
        parent::setup();

        $this->post = create('Knot\Models\TextPost')->post;
        $this->authenticate();
    }

    /** @test */
    public function a_user_can_fetch_their_notifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson('/api/notifications')->json()
        );
    }

    /** @test */
    public function a_user_can_mark_their_notifications_as_read()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1, auth()->user()->unreadNotifications);
        $this->deleteJson('/api/notifications')->assertStatus(204);
        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
