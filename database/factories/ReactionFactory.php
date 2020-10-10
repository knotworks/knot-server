<?php
namespace Database\Factories;

use Knot\Models\User;
use Knot\Models\Post;
use Knot\Models\Reaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reaction::class;

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
            'type' => Reaction::REACTIONS[array_rand(Reaction::REACTIONS)],
        ];
    }
}
