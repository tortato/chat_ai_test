<?php
namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'user_message' => $this->faker->sentence,
            'ai_response' => $this->faker->paragraph,
        ];
    }
}
