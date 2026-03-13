<?php

declare(strict_types=1);

use App\Jobs\ProcessAvatarUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->currentPassword = 'abigsecret';
    $this->newPassword = 'anewpassword';
    $this->user = create('App\Models\User', ['password' => $this->currentPassword]);

    $this->login($this->user);

    Storage::fake('media-temp');
    Storage::fake('r2');
    config([
        'media.disk' => 'r2',
        'media.temporary_disk' => 'media-temp',
    ]);
});

it('allows a user to update their profile information', function (): void {
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
});

it('requires the current password before updating the password', function (): void {
    $newInfo = [
        'first_name' => 'Jane Doe',
        'email' => 'jane@janedoe.com',
        'current_password' => 'incorrectoldpassword',
        'password' => $this->newPassword,
        'password_confirmation' => $this->newPassword,
    ];

    $this->putJson('api/profile/update', $newInfo)->assertStatus(422);
});

it('requires the password confirmation to match', function (): void {
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
});

it('prevents an empty first name', function (): void {
    $this->withExceptionHandling();

    $profile = [
        'first_name' => '',
        'email' => 'jane@doe.com',
    ];

    $this->putJson('api/profile/update', $profile)
        ->assertStatus(422)
        ->assertJsonValidationErrors('first_name');
});

it('requires a valid email address', function (): void {
    $profile = [
        'first_name' => 'Jane Doe',
        'email' => 'jane123',
    ];

    $this->putJson('api/profile/update', $profile)
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

it('allows a user to set their avatar', function (): void {
    Queue::fake();
    $avatar = UploadedFile::fake()->image('avatar.jpg', 200, 200);

    $this->putJson('/api/profile/avatar', ['avatar' => $avatar])->assertStatus(200);

    expect(auth()->user()->fresh()->avatar_status)->toBe('pending');
    expect(auth()->user()->fresh()->avatar_temporary_path)->not()->toBeNull();

    Queue::assertPushed(ProcessAvatarUpload::class);
});

it('requires a file when updating an avatar', function (): void {
    $this->putJson('/api/profile/avatar', ['avatar' => null])->assertStatus(422);
    $this->putJson('/api/profile/avatar', [])->assertStatus(422);
});
