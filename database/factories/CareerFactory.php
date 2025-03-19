<?php

namespace Database\Factories;

use App\Enums\CareerStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Career>
 */
class CareerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $publishedAt = $this->faker->optional()->dateTimeBetween('-1 year', 'now');

        return [
            'title' => $this->faker->jobTitle(),
            'subtitle' => $this->faker->optional()->sentence(),
            'description' => $this->faker->paragraphs(5, true),
            'location' => $this->faker->city(),
            'format' => $this->faker->randomElement(['remote', 'onsite', 'hybrid']),
            'department' => $this->faker->optional()->randomElement(['Engineering', 'Sales', 'Marketing', 'HR', 'Finance']),
            'employment_type' => $this->faker->randomElement(['full-time', 'part-time', 'contract']),
            'salary_min' => $this->faker->optional()->numberBetween(30000, 80000),
            'salary_max' => function (array $attributes) {
                return $attributes['salary_min']
                    ? $this->faker->numberBetween($attributes['salary_min'], $attributes['salary_min'] + 50000)
                    : null;
            },
            'currency' => $this->faker->currencyCode(),
            'application_email' => $this->faker->companyEmail(),
            'requirements' => $this->faker->optional()->paragraphs(3, true),
            'benefits' => $this->faker->optional()->paragraphs(2, true),
            'banner_image' => 'careers/banners/placeholder-' . Str::random(10) . '.jpg',
            'status' => $this->faker->randomElement(CareerStatusEnum::values()),
            'published_at' => $publishedAt,
            'expires_at' => $publishedAt ? $this->faker->optional()->dateTimeBetween($publishedAt, '+1 year') : null,
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the career posting is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CareerStatusEnum::PUBLISHED->value,
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the career posting is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CareerStatusEnum::DRAFT->value,
            'published_at' => null,
            'expires_at' => null,
        ]);
    }

    /**
     * Indicate that the career posting is closed.
     */
    public function closed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CareerStatusEnum::CLOSED->value,
            'expires_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the career posting is archived.
     */
    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CareerStatusEnum::ARCHIVED->value,
        ]);
    }
}
