<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReactionTest extends TestCase
{
    use RefreshDatabase;

    protected $reaction;

    public function setup(): void
    {
        parent::setUp();

        $this->reaction = create('Knot\Models\Reaction');
    }

    /** @test */
    public function a_reaction_belongs_to_a_post()
    {
        $this->assertInstanceOf(
            'Knot\Models\Post',
            $this->reaction->post
        );
    }

    /** @test */
    public function a_reaction_belongs_to_a_user()
    {
        $this->assertInstanceOf(
            'Knot\Models\User',
            $this->reaction->user
        );
    }
}
