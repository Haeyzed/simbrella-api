<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            BlogPostSeeder::class,
            CareerSeeder::class,
            HomePageManagementSeeder::class,
            ContactInformationSeeder::class,
            MessageSeeder::class,
            PageSeeder::class,
            PageImageSeeder::class,
            VisitorSeeder::class,
        ]);
//        User::factory()->create([
//            'name' => 'Muibi Azeez Abolade',
//            'email' => 'muibi.azeezabolade@example.com',
//            'password' => Hash::make('password'),
//            'status' => StatusEnum::ACTIVE->value,
//        ]);
    }
}
