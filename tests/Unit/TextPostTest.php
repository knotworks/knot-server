<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TextPostTest extends TestCase
{
    use RefreshDatabase;

    protected $textPost;

    public function setup()
    {
        parent::setUp();

        $this->authenticate();
        $this->textPost = create('Knot\Models\TextPost', ['user_id' => auth()->id()]);
    }

    /** @test */
    public function a_text_post_belongs_to_a_user()
    {
        $this->assertInstanceOf('Knot\Models\User', $this->textPost->user);
    }

    /** @test */
    public function a_text_has_an_associated_generic_post()
    {
        $this->assertInstanceOf('Knot\Models\Post', $this->textPost->post);
    }
}
