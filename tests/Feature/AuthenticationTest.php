<?php

namespace Tests\Feature;

use Tests\TestCase;
use Knot\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_register_a_new_user()
    {
        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@janedoe.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar',
        ];

        $this->postJson('api/auth/user/register', $userData)->assertStatus(201);
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function can_fetch_an_authenticated_user()
    {
        $this->authenticate();

        $this->getJson('api/auth/user')->assertStatus(200);
    }
}
