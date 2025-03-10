<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        ]);
//        User::factory()->create([
//            'name' => 'Muibi Azeez Abolade',
//            'email' => 'muibi.azeezabolade@example.com',
//            'password' => Hash::make('password'),
//            'status' => StatusEnum::ACTIVE->value,
//        ]);
    }
}
