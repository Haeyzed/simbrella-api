<?php

namespace Database\Factories;

use App\Enums\SectionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientSection>
 */
class ClientSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'logo_path' => 'clients/logo-' . Str::random(10) . '.png',
            'order' => $this->faker->numberBetween(0, 10),
            'status' => $this->faker->randomElement(SectionStatusEnum::values()),
            'user_id' => User::factory(),
        ];
    }
}
