<?php
// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Knot\Models\User;
use Doorman;

class AuthenticatesUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_register_a_new_user()
    {
        $this->withExceptionHandling();

        $userData =  [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@janedoe.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar',
        ];

        $this->postJson('api/auth/user', $userData)->assertStatus(200);
        $this->assertCount(1, User::all());
    }

    /** @test */
    function can_fetch_an_authenticated_user()
    {
        $this->authenticate();

        $this->getJson('api/auth/user')->assertStatus(200);
    }

}
