<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatesTextPostTest extends TestCase
{
    use RefreshDatabase;

    public function setup()
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_user_can_create_a_text_post()
    {
        $postContent = ['body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.'];

        $this->json('POST', 'api/posts/new/text', $postContent)
            ->assertStatus(200)
            ->assertJson($postContent);
    }
}
