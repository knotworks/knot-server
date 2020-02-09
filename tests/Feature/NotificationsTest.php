<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected $post;

    public function setup(): void
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
