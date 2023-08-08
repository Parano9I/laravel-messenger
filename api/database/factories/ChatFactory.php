<?php

namespace Database\Factories;

use App\Enums\ChatType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = array_rand(array_flip(array_column(ChatType::cases(), 'value')));
        $isOpen = (boolean)($type === 'personal' ? false : rand(0, 1));

        return [
            'name'    => fake()->company(),
            'type'    => $type,
            'is_open' => $isOpen,
            'avatar'  => null
        ];
    }
}
