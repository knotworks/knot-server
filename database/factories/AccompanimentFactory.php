<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Knot\Models\Accompaniment;
use Knot\Models\Post;
use Knot\Models\User;

class AccompanimentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Accompaniment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'name' => $this->faker->name,
        ];
    }
}
