<?php

declare(strict_types=1);

use App\Models\User;

it('allows a user to log in', function (): void {
    $user = create(User::class, ['password' => 'password']);
    $credentials = [
        'email' => $user->email,
        'password' => 'password',
    ];

    $this->post('login', $credentials)->assertStatus(204);

    expect(auth()->user()->is($user))->toBeTrue();
});

it('allows a user to log out', function (): void {
    $this->login();

    $this->post('logout')->assertStatus(204);

    $this->assertGuest('web');
});
