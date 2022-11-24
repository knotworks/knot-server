<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Knot\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_login()
    {
        $user = create(User::class, ['password' => 'password']);
        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $this->post('login', $credentials)->assertStatus(204);

        $this->assertTrue(auth()->user()->is($user));
    }

    /** @test */
    public function a_user_can_logout()
    {
        $this->login();

        $this->post('logout')->assertStatus(204);

        $this->assertGuest('web');
    }
}
