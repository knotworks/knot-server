<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Knot\Models\Post;
use Knot\Models\PostMedia;

class PostMediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_id' => Post::factory(),
            'path' => 'media/'.$this->faker->md5(),
            'width' => 1200,
            'height' => 800,
            'type' => 'image',
        ];
    }
}
