<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $currentPassword;
    protected $newPassword;

    public function setup(): void
    {
        parent::setup();

        $this->currentPassword = 'abigsecret';
        $this->newPassword = 'anewpassword';
        $this->user = create('Knot\Models\User', ['password' => $this->currentPassword]);

        $this->login($this->user);
    }

    /** @test */
    public function a_user_can_update_their_profile_information()
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

        $this->putJson('api/profile/update', $newInfo)->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_update_their_password_unless_they_provide_their_current_one()
    {
        $newInfo = [
            'first_name' => 'Jane Doe',
            'email' => 'jane@janedoe.com',
            'current_password' => 'incorrectoldpassword',
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
        ];

        $this->putJson('api/profile/update', $newInfo)->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_update_their_password_if_it_doesnt_match_the_confirmation_field()
    {
        $newInfo = [
            'first_name' => 'Jane Doe',
            'email' => 'jane@janedoe.com',
            'current_password' => $this->currentPassword,
            'password' => $this->newPassword,
            'password_confirmation' => 'doesnotmatch',
        ];

        $this->putJson('api/profile/update', $newInfo)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function a_user_cannot_have_an_empty_first_name()
    {
        $this->withExceptionHandling();
        $profile = [
            'first_name' => '',
            'email' => 'jane@doe.com',
        ];

        $this->putJson('api/profile/update', $profile)
            ->assertStatus(422)
            ->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function a_user_must_provide_a_valid_email_address()
    {
        $profile = [
            'first_name' => 'Jane Doe',
            'email' => 'jane123',
        ];
        $this->putJson('api/profile/update', $profile)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function a_user_can_set_their_avatar()
    {
        $avatarPath = 'testing/media/rX47fHdhjqwhL';

        $this->putJson('/api/profile/avatar', ['avatar' => $avatarPath])->assertStatus(200);
        $this->assertEquals(auth()->user()->avatar, $avatarPath);
    }

    /** @test */
    public function updating_an_avatar_requires_a_path()
    {
        $this->putJson('/api/profile/avatar', ['avatar' => null])->assertStatus(422);
        $this->putJson('/api/profile/avatar', [])->assertStatus(422);
    }
}
