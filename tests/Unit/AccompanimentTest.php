<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccompanimentTest extends TestCase
{
    use RefreshDatabase;

    protected $accompaniment;

    public function setup()
    {
        parent::setUp();

        $this->accompaniment = create('Knot\Models\Accompaniment');
    }

    /** @test */
    public function an_accompaniment_belongs_to_a_post()
    {
        $this->assertInstanceOf(
            'Knot\Models\Post',
            $this->accompaniment->post
        );
    }

    /** @test */
    public function an_accompaniment_with_a_user_id_can_fetch_its_user()
    {
        $secondAccompaniment = create('Knot\Models\Accompaniment', ['user_id' => null]);

        $this->assertInstanceOf(
            'Knot\Models\User',
            $this->accompaniment->user
        );

        $this->assertNull($secondAccompaniment->user);
    }
}
