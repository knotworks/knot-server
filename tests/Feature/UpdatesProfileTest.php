<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UpdatesProfileTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $currentPassword;
    protected $newPassword;

    public function setup()
    {
        parent::setup();
        $this->currentPassword = 'abigsecret';
        $this->newPassword = 'anewpassword';
        $this->user = create('Knot\Models\User', ['password' => $this->currentPassword]);

        Passport::actingAs($this->user);
    }

    /** @test */
    function a_user_can_update_their_profile_information()
    {
        $this->withExceptionHandling();

        $newInfo = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@doe.com',
        'current_password' => $this->currentPassword,
        'password' => $this->newPassword,
        'password_confirmation' => $this->newPassword,
        ];

        $response = $this->putJson('api/profile/update', $newInfo);

        $response->assertStatus(200);
    }

    /** @test */
    function a_user_cannot_update_their_password_unless_they_provide_their_current_one()
    {
        $this->withExceptionHandling();

        $newInfo = [
        'first_name' => 'Jane Doe',
        'email' => 'jane@janedoe.com',
        'current_password' => 'incorrectoldpassword',
        'password' => $this->newPassword,
        'password_confirmation' => $this->newPassword,
        ];

        $response = $this->putJson('api/profile/update', $newInfo);

        $response->assertStatus(422);
    }

    /** @test */
    function a_user_cannot_update_their_password_if_it_doesnt_match_the_confirmation_field()
    {
        $this->withExceptionHandling();

        $newInfo = [
        'first_name' => 'Jane Doe',
        'email' => 'jane@janedoe.com',
        'current_password' => $this->currentPassword,
        'password' => $this->newPassword,
        'password_confirmation' => 'doesnotmatch',
        ];

        $response = $this->putJson('api/profile/update', $newInfo);

        $response->assertStatus(422);
        $this->assertTrue(array_key_exists('password', $response->getOriginalContent()));
    }

    /** @test */
    function a_user_cannot_have_an_empty_first_name()
    {
        $this->withExceptionHandling();
        $profile = [
            'first_name' => '',
            'email' => 'jane@doe.com'
        ];

        $response = $this->putJson('api/profile/update', $profile);

        $response->assertStatus(422);
        $this->assertTrue(array_key_exists('first_name', $response->getOriginalContent()));
    }

    /** @test */
    function a_user_must_provide_a_valid_email_address()
    {
        $this->withExceptionHandling();
        $profile = [
            'first_name' => 'Jane Doe',
            'email' => 'jane123'
        ];

        $response = $this->putJson('api/profile/update', $profile);

        $response->assertStatus(422);
        $this->assertTrue(array_key_exists('email', $response->getOriginalContent()));
    }
}
