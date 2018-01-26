<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoPostTest extends TestCase
{
    use RefreshDatabase;

    protected $post;

    public function setup()
    {
        parent::setUp();

        $this->authenticate();
        $this->post = create('Knot\Models\PhotoPost', ['user_id' => auth()->id()]);
    }

    /** @test */
    public function a_photo_post_returns_the_proper_url_depending_on_if_its_uploaded_to_the_cloud_or_not()
    {
        Storage::fake(config('filesystems.cloud'));

        $this->assertEquals(asset('images/tmp/' . $this->post->image_path), $this->post->image_url);

        $this->post->cloud = true;
        $this->post->save();

        $this->assertEquals('/storage/' . $this->post->image_path, $this->post->image_url);
    }
}
