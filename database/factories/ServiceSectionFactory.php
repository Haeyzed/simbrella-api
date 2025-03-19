<?php

namespace Database\Factories;

use App\Enums\SectionStatusEnum;
use App\Models\ServiceSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceSection>
 */
class ServiceSectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceSection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'title_short' => $this->faker->words(2, true),
            'summary' => $this->faker->paragraph(3),
            'summary_short' => $this->faker->sentence(),
            'icon_path' => null,
            'image_path' => null,
            'order' => $this->faker->numberBetween(0, 10),
            'status' => $this->faker->randomElement(SectionStatusEnum::values()),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the service section is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => SectionStatusEnum::PUBLISHED->value,
        ]);
    }

    /**
     * Indicate that the service section is draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => SectionStatusEnum::DRAFT->value,
        ]);
    }

    /**
     * Indicate that the service section has an image.
     */
    public function withImage(): static
    {
        return $this->state(fn(array $attributes) => [
            'icon_path' => 'services/service-' . Str::random(10) . '.jpg',
            'image_path' => 'services/service-' . Str::random(10) . '.jpg',
        ]);
    }
}
