<?php
// @codingStandardsIgnoreFile

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PhotoPostTest extends TestCase
{
  use DatabaseMigrations;

  protected $post;

  public function setup()
  {
    parent::setUp();


    $this->authenticate();
    $this->post = create('Knot\Models\PhotoPost', ['user_id' => auth()->id()]);
  }

  /** @test */
  function a_photo_post_returns_the_proper_url_depending_on_if_its_uploaded_to_the_cloud_or_not()
  {
    Storage::fake(config('filesystems.cloud'));

    $this->assertEquals(asset('images/tmp/' . $this->post->image_path), $this->post->image_url);

    $this->post->cloud = true;
    $this->post->save();

    $this->assertEquals('/storage/' . $this->post->image_path, $this->post->image_url);
  }


}
