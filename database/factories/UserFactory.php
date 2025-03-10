<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->paragraph(),
            'country' => $this->faker->country(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password for testing
            'status' => StatusEnum::ACTIVE->value,
            'profile_image' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::INACTIVE->value,
        ]);
    }

    /**
     * Indicate that the user is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::SUSPENDED->value,
        ]);
    }

    /**
     * Indicate that the user is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::PENDING->value,
        ]);
    }

    /**
     * Indicate that the user has a profile image.
     */
    public function withProfileImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_image' => 'users/profile-' . Str::random(10) . '.jpg',
        ]);
    }

    /**
     * Indicate that the user has no bio.
     */
    public function withoutBio(): static
    {
        return $this->state(fn (array $attributes) => [
            'bio' => null,
        ]);
    }

    /**
     * Indicate that the user has no address information.
     */
    public function withoutAddress(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => null,
            'state' => null,
            'postal_code' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('super-admin');
        });
    }
}
