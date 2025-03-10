<?php

namespace Database\Factories;

use App\Models\HeroSection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeroImage>
 */
class HeroImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hero_section_id' => HeroSection::factory(),
            'image_path' => 'hero/images/placeholder-' . Str::random(10) . '.jpg',
        ];
    }
}
