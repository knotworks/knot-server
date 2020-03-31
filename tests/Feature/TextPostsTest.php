<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextPostsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_user_can_create_a_text_post()
    {
        $postContent = ['body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.'];

        $this->json('POST', 'api/posts/new/text', $postContent)
            ->assertStatus(201)
            ->assertJson($postContent);
    }
}
