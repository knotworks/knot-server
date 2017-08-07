<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UpdatesAvatarTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    function a_user_can_update_their_avatar()
    {
        Storage::fake(config('filesystems.cloud'));

        $response = $this->putJson('api/profile/avatar', [
          'avatar' => UploadedFile::fake()->image('avatar.jpg', 700, 700),
        ]);
        $imagePath = $response->getOriginalContent()->first()->profile_image;

        $this->assertEquals($imagePath, auth()->user()->profile_image);
        Storage::cloud()->assertExists($imagePath);
    }
}
