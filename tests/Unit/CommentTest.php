<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected $comment;

    public function setup(): void
    {
        parent::setUp();

        $this->comment = create('Knot\Models\Comment');
    }

    /** @test */
    public function a_comment_belongs_to_a_post()
    {
        $this->assertInstanceOf(
            'Knot\Models\Post',
            $this->comment->post
        );
    }

    /** @test */
    public function a_comment_belongs_to_a_user()
    {
        $this->assertInstanceOf(
            'Knot\Models\User',
            $this->comment->user
        );
    }
}
