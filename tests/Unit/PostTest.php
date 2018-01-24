<?php

// @codingStandardsIgnoreFile

namespace Tests\Unit;

use Tests\TestCase;
use Knot\Models\Location;
use Knot\Models\Reaction;
use Knot\Models\Accompaniment;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PostTest extends TestCase
{
    use DatabaseMigrations;

    protected $post;

    public function setup()
    {
        parent::setUp();

        $this->authenticate();
        $this->post = create('Knot\Models\TextPost', ['user_id' => auth()->id()])->post;
    }

    /** @test */
    public function a_post_can_have_accompaniments()
    {
        $this->assertInstanceOf(
      'Illuminate\Database\Eloquent\Collection',
      $this->post->accompaniments
    );
    }

    /** @test */
    public function a_post_can_add_accompaniments()
    {
        $this->post->addAccompaniments([
      [
        'user_id' => 2,
        'name' => 'Jane Doe',
      ],
      [
        'user_id' => null,
        'name' => 'Someone Else',
      ],
    ]);

        $this->assertCount(2, $this->post->accompaniments);
    }

    /** @test */
    public function a_post_can_have_reactions()
    {
        $this->assertInstanceOf(
      'Illuminate\Database\Eloquent\Collection',
      $this->post->reactions
    );
    }

    /** @test */
    public function a_post_can_add_reactions()
    {
        $reaction = make('Knot\Models\Reaction')->toArray();
        $this->post->addReaction($reaction);

        $this->assertCount(1, $this->post->reactions);

        $this->assertDatabaseHas('reactions', [
      'type' => $reaction['type'],
      'user_id' => $reaction['user_id'],
      'post_id' => $this->post->id,
    ]);
    }

    /** @test */
    public function a_post_will_delete_its_associations_if_it_is_deleted()
    {
        // Attach various relations to the post
        $location = make('Knot\Models\Location', [
      'user_id' => auth()->id(),
      'locatable_id' => $this->post->id,
      'locatable_type' => get_class($this->post),
    ])->toArray();

        $this->post->addAccompaniments([
      [
        'user_id' => 2,
        'name' => 'Jeff Henderson',
      ],
    ]);

        $this->post->addReaction([
      'user_id' => 2,
      'type' => Reaction::REACTIONS['smile'],
    ]);

        $this->post->addLocation($location);

        // Then delete the post
        $this->post->delete();

        // Assert there are zero records of the relation
        $this->assertEquals(0, Accompaniment::count());
        $this->assertEquals(0, Location::count());
        $this->assertEquals(0, Reaction::count());
    }
}
