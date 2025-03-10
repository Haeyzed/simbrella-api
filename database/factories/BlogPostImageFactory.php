<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPostImage>
 */
class BlogPostImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $order = 1;

        return [
            'blog_post_id' => BlogPost::factory(),
            'image_path' => 'blog/images/placeholder-' . Str::random(10) . '.jpg',
            'order' => $order++,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function () {
            // Reset order counter after each creation
            static $order = 1;
        });
    }
}
