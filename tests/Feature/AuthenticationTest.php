<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Knot\Models\User;
use Tests\TestCase;

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

        $this->postJson('register', $userData)->assertStatus(201);
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function can_fetch_an_authenticated_user()
    {
        $this->authenticate();

        $this->getJson('api/user')->assertStatus(200);
    }
}
