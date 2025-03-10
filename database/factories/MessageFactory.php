<?php

namespace Database\Factories;

use App\Enums\MessageStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'message' => $this->faker->paragraph(),
            'response' => null,
            'status' => MessageStatusEnum::UNREAD->value,
            'responded_by_id' => null,
            'responded_at' => null,
        ];
    }

    /**
     * Indicate that the message has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MessageStatusEnum::READ->value,
        ]);
    }

    /**
     * Indicate that the message has been responded to.
     */
    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'response' => $this->faker->paragraph(),
            'status' => MessageStatusEnum::RESPONDED->value,
            'responded_by_id' => User::factory(),
            'responded_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the message has been archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MessageStatusEnum::ARCHIVED->value,
        ]);
    }
}
