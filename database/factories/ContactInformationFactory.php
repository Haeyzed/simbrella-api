<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactInformation>
 */
class ContactInformationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'facebook_link' => $this->faker->url(),
            'instagram_link' => $this->faker->url(),
            'linkedin_link' => $this->faker->url(),
            'twitter_link' => $this->faker->url(),
            'user_id' => User::factory(),
        ];
    }
}
