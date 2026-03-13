<?php

declare(strict_types=1);

use App\Models\User;

it('can register a new user', function (): void {
    $userData = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@janedoe.com',
        'password' => 'foobar',
        'password_confirmation' => 'foobar',
    ];

    $this->postJson('api/register', $userData)->assertStatus(201);

    expect(User::count())->toBe(1);
});

it('can fetch an authenticated user', function (): void {
    $this->login();

    $this->getJson('api/user')->assertStatus(200);
});
