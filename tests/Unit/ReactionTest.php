<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReactionTest extends TestCase
{
  use DatabaseMigrations;

  protected $reaction;

  public function setup()
  {
      parent::setUp();
      
      $this->reaction = create('Knot\Models\Reaction');
  }

  /** @test */
  function a_reaction_belongs_to_a_post()
  {
    $this->assertInstanceOf(
        'Knot\Models\Post', $this->reaction->post
    );
  }

  /** @test */
  function a_reaction_belongs_to_a_user()
  {
    $this->assertInstanceOf(
        'Knot\Models\User', $this->reaction->user
    );
  }
}
