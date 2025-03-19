<?php

namespace Database\Factories;

use App\Enums\SectionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeroSection>
 */
class HeroSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'subtitle' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(SectionStatusEnum::values()),
            'user_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => SectionStatusEnum::PUBLISHED->value,
        ]);
    }
}
