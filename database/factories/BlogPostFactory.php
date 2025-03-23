<?php

namespace Database\Factories;

use App\Enums\BlogPostStatusEnum;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogPost>
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
            'subtitle' => $this->faker->sentence(),
            'body' => $this->faker->paragraphs(5, true),
            'banner_image' => 'public/blog/banners/' . $this->faker->image('public/storage/blog/banners', 1200, 600, null, false),
            'caption' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(BlogPostStatusEnum::cases())->value,
            'user_id' => User::factory(),
            'views' => $this->faker->numberBetween(0, 5000),
        ];
    }

    /**
     * Indicate that the blog post is published.
     *
     * @return Factory
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => BlogPostStatusEnum::PUBLISHED->value,
            ];
        });
    }

    /**
     * Indicate that the blog post is draft.
     *
     * @return Factory
     */
    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => BlogPostStatusEnum::DRAFT->value,
            ];
        });
    }

    /**
     * Indicate that the blog post is archived.
     *
     * @return Factory
     */
    public function archived(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => BlogPostStatusEnum::ARCHIVED->value,
            ];
        });
    }
}
