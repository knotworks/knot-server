<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreatesPhotoPostsTest extends TestCase
{
    use DatabaseMigrations;

    public function setup()
    {
          parent::setup();
          
          $this->authenticate();
    }

    /** @test */
    function a_photo_post_uploads_the_passed_in_file_to_the_storage_disk()
    {
        // $this->withExceptionHandling();
        Storage::fake('b2');
        
        $response = $this->json('POST', 'api/posts/new/photo', [
          'body' => 'My fancy photo post',
          'image' => UploadedFile::fake()->image('french-river.jpg', 1200, 900),
        ]);
        $imagePath = $response->getOriginalContent()->first()->image_path;

        Storage::disk('b2')->assertExists($imagePath);
    }
}