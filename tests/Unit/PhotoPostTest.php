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
    public function a_photo_post_belongs_to_a_user()
    {
        $this->assertInstanceOf(
      'Knot\Models\User',
      $this->post->user
    );
    }
}
