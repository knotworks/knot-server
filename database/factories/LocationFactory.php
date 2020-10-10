<?php
namespace Database\Factories;

use Knot\Models\Post;
use Knot\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'locatable_id' => Post::factory(),
            'locatable_type' => Post::class,
            'source' => 'foursquare',
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'city' => $this->faker->city,
            'name' => $this->faker->company,
        ];
    }
}
