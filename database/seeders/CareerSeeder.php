<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\User;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first();

        if ($admin) {
            // Create published careers
            Career::factory()
                ->count(5)
                ->published()
                ->create([
                    'user_id' => $admin->id,
                ]);

            // Create draft careers
            Career::factory()
                ->count(3)
                ->draft()
                ->create([
                    'user_id' => $admin->id,
                ]);

            // Create closed careers
            Career::factory()
                ->count(2)
                ->closed()
                ->create([
                    'user_id' => $admin->id,
                ]);

            // Create archived careers
            Career::factory()
                ->count(2)
                ->archived()
                ->create([
                    'user_id' => $admin->id,
                ]);
        }
    }
}
