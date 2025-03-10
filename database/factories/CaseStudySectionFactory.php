<?php

namespace Database\Factories;

use App\Enums\SectionStatusEnum;
use App\Models\ClientSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseStudySection>
 */
class CaseStudySectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_section_id' => ClientSection::factory(),
            'banner_image' => 'case-studies/banner-' . Str::random(10) . '.jpg',
            'company_name' => $this->faker->company(),
            'subtitle' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'challenge' => $this->faker->optional()->paragraphs(2, true),
            'solution' => $this->faker->optional()->paragraphs(2, true),
            'results' => $this->faker->optional()->paragraphs(2, true),
            'status' => $this->faker->randomElement(SectionStatusEnum::values()),
            'user_id' => User::factory(),
        ];
    }
}
