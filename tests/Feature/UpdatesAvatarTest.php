<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Image;
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
    public function a_user_can_update_their_avatar()
    {
        $cloud = config('filesystems.cloud');
        Storage::fake($cloud);

        $response = $this->putJson('api/profile/avatar', [
          'avatar' => UploadedFile::fake()->image('avatar.jpg', 700, 700),
        ]);
        $imagePath = $response->getOriginalContent()->profile_image;

        $this->assertEquals($imagePath, auth()->user()->profile_image);
        Storage::cloud()->assertExists($imagePath);
    }

    /** @test */
    public function an_upload_profile_image_gets_cropped_to_a_square()
    {
        $cloud = config('filesystems.cloud');
        Storage::fake($cloud);

        $response = $this->putJson('api/profile/avatar', [
          'avatar' => UploadedFile::fake()->image('myavatar.jpg', 1200, 900),
        ]);
        $imagePath = $response->getOriginalContent()->profile_image;
        $storagePath = storage_path().'/framework/testing/disks/'.$cloud.'/'.$imagePath;

        $avatar = Image::make($storagePath);

        $this->assertEquals($avatar->width(), 600);
        $this->assertEquals($avatar->height(), 600);

        $avatar->destroy();
    }
}
