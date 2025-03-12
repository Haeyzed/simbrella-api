<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::query()->firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@example.com',
                'phone' => '+1 (555) 123-4567',
                'bio' => 'Super administrator with full system access.',
                'country' => 'United States',
                'state' => 'California',
                'postal_code' => '90210',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => StatusEnum::ACTIVE->value,
            ]
        );

        $superAdmin->assignRole(config('acl.roles.sadmin.name'));

        // Create admin user
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'phone' => '+1 (555) 234-5678',
                'bio' => 'Administrator with management privileges.',
                'country' => 'United States',
                'state' => 'New York',
                'postal_code' => '10001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => StatusEnum::ACTIVE->value,
            ]
        );

        $admin->assignRole(config('acl.roles.admin.name'));

        // Create editor user
        $editor = User::query()->firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'first_name' => 'Editor',
                'last_name' => 'User',
                'email' => 'editor@example.com',
                'phone' => '+1 (555) 345-6789',
                'bio' => 'Content editor with publishing rights.',
                'country' => 'United States',
                'state' => 'Texas',
                'postal_code' => '75001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => StatusEnum::ACTIVE->value,
            ]
        );

        $editor->assignRole(config('acl.roles.editor.name'));

        // Create regular user
        $user = User::query()->firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'first_name' => 'Regular',
                'last_name' => 'User',
                'email' => 'user@example.com',
                'phone' => '+1 (555) 456-7890',
                'bio' => 'Standard user with basic access.',
                'country' => 'United States',
                'state' => 'Florida',
                'postal_code' => '33101',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => StatusEnum::ACTIVE->value,
            ]
        );

        $user->assignRole(config('acl.roles.user.name'));

        // Create additional users for testing if in development environment
//        if (app()->environment('local', 'development', 'testing')) {
//            User::factory()
//                ->count(10)
//                ->create()
//                ->each(function ($user) {
//                    $user->assignRole('user');
//                });
//        }
    }
}
