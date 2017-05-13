<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AccompanimentTest extends TestCase
{
  use DatabaseMigrations;

  protected $accompaniment;

  public function setup()
  {
      parent::setUp();
      
      $this->accompaniment = create('FamJam\Models\Accompaniment');
  }

  /** @test */
  function an_accompaniment_belongs_to_a_post()
  {
    $this->assertInstanceOf(
        'FamJam\Models\Post', $this->accompaniment->post
    );
  }

  /** @test */
  function an_accompaniment_with_a_user_id_can_fetch_its_user()
  {
    $secondAccompaniment = create('FamJam\Models\Accompaniment', ['user_id' => null]);
    
    $this->assertInstanceOf(
        'FamJam\Models\User', $this->accompaniment->user()
    );
    
    $this->assertNull($secondAccompaniment->user());
  }
}
