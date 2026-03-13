<?php

declare(strict_types=1);

use Database\Factories\DatabaseNotificationFactory;

beforeEach(function (): void {
    $this->post = create('App\Models\Post');
    $this->login();
});

it('allows a user to fetch their notifications', function (): void {
    DatabaseNotificationFactory::new()->create();

    expect($this->getJson('/api/notifications')->json()['data'])->toHaveCount(1);
});

it('allows a user to mark their notifications as read', function (): void {
    DatabaseNotificationFactory::new()->create();

    expect(auth()->user()->unreadNotifications)->toHaveCount(1);

    $this->deleteJson('/api/notifications')->assertStatus(204);

    expect(auth()->user()->fresh()->unreadNotifications)->toHaveCount(0);
});
