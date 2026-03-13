<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'path' => 'media/' . $this->faker->md5() . '.jpg',
            'disk' => 'r2',
            'width' => 1200,
            'height' => 800,
            'type' => 'image',
            'status' => 'ready',
            'mime_type' => 'image/jpeg',
            'bytes' => 1024,
        ];
    }
}
