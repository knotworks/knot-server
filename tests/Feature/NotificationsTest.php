<?php

namespace Tests\Feature;

use Database\Factories\DatabaseNotificationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected $post;

    public function setup(): void
    {
        parent::setup();

        $this->post = create('Knot\Models\Post');
        $this->login();
    }

    /** @test */
    public function a_user_can_fetch_their_notifications()
    {
        DatabaseNotificationFactory::new()->create();

        $this->assertCount(
            1,
            $this->getJson('/api/notifications')->json()['data']
        );
    }

    /** @test */
    public function a_user_can_mark_their_notifications_as_read()
    {
        DatabaseNotificationFactory::new()->create();

        $this->assertCount(1, auth()->user()->unreadNotifications);
        $this->deleteJson('/api/notifications')->assertStatus(204);
        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
