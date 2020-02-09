<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoPostsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_photo_post_uploads_the_passed_in_file_to_the_storage_disk()
    {
        Cloudder::spy();
        Cloudder::shouldReceive('getPublicId')->andReturn('photo-posts/12345');

        $fakeFile = UploadedFile::fake()->image('french-river.jpg', 1200, 900);

        $response = $this->json('POST', 'api/posts/new/photo', [
            'body' => 'My fancy photo post',
            'image' => $fakeFile,
        ]);
        $imagePath = $response->getOriginalContent()->image_path;

        Cloudder::shouldHaveReceived('upload')->with(Mockery::type('string'), Mockery::type('string'))->once();

        $this->assertDatabaseHas('photo_posts', [
            'image_path' => $imagePath,
        ]);
    }
}
