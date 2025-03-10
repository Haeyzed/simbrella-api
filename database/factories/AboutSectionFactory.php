<?php

namespace Database\Factories;

use App\Enums\SectionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AboutSection>
 */
class AboutSectionFactory extends Factory
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
            'summary' => $this->faker->paragraph(),
            'image_path' => 'about/about-' . Str::random(10) . '.jpg',
            'status' => $this->faker->randomElement(SectionStatusEnum::values()),
            'user_id' => User::factory(),
        ];
    }
}
