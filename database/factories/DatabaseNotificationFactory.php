<?php
namespace Database\Factories;

use Knot\Models\User;
use Ramsey\Uuid\Uuid;
use Knot\Notifications\PostCommentedOn;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatabaseNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'type' => PostCommentedOn::class,
            'notifiable_id' => function () {
                return auth()->id() ?: User::factory()->id;
            },
            'notifiable_type' => User::class,
            'data' => ['foo' => 'bar'],
        ];
    }
}
