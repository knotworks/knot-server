<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Knot\Jobs\UploadPhotoPostImageToCloud;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoPostsTest extends TestCase
{
    use RefreshDatabase;

    public function setup()
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_photo_post_uploads_the_passed_in_file_to_the_storage_disk()
    {
        Storage::fake(config('filesystems.cloud'));
        Queue::fake();

        $response = $this->json('POST', 'api/posts/new/photo', [
            'body' => 'My fancy photo post',
            'image' => UploadedFile::fake()->image('french-river.jpg', 1200, 900),
        ]);
        $post = $response->getOriginalContent();
        $imagePath = $post->image_path;

        Queue::assertPushed(UploadPhotoPostImageToCloud::class);

        $this->assertDatabaseHas('photo_posts', [
            'image_path' => $imagePath,
        ]);

        unlink($imagePath);
    }
}
