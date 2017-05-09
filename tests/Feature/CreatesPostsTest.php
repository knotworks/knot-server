<?php

namespace Tests\Feature;

use App\Activity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreatesTextPostTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_create_a_text_post()
    {
        $this->authenticate();
        
        $postContent =  ['body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.'];
        $response = $this->json('POST', 'api/posts/new/text', $postContent);

        $response
        ->assertStatus(200)
        ->assertJson($postContent);
    }
    
}