<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use JD\Cloudder\Facades\Cloudder;
use Knot\Services\MediaUploadService;
use Tests\TestCase;

class MediaUploadServiceTest extends TestCase
{
    protected $uploadService;

    public function setup(): void
    {
        parent::setup();

        $this->uploadService = new MediaUploadService();
    }

    /** @test */
    public function it_uploads_an_image_to_the_cloud()
    {
        Cloudder::partialMock();
        Cloudder::shouldReceive('upload->getPublicId')->andReturn('testing/media/images/photo');

        $fakeFile = UploadedFile::fake()->image('photo.jpg', 1280, 720);

        $upload = $this->uploadService->uploadImage($fakeFile);

        $this->assertEquals($upload, [
            'publicId' => 'testing/media/images/photo',
            'width' => 1280,
            'height'=> 720,
        ]);
    }
}
