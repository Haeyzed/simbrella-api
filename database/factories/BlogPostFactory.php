<?php

namespace Database\Factories;

use App\Enums\BlogPostStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
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
            'subtitle' => $this->faker->optional(0.7)->sentence(),
            'body' => $this->faker->paragraphs(5, true),
            'banner_image' => 'blog/banners/placeholder-' . Str::random(10) . '.jpg',
            'caption' => $this->faker->optional(0.5)->sentence(),
            'status' => $this->faker->randomElement(BlogPostStatusEnum::values()),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the blog post is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => BlogPostStatusEnum::PUBLISHED->value,
        ]);
    }

    /**
     * Indicate that the blog post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => BlogPostStatusEnum::DRAFT->value,
        ]);
    }

    /**
     * Indicate that the blog post is archived.
     */
    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => BlogPostStatusEnum::ARCHIVED->value,
        ]);
    }
}
