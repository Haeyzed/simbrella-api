<?php

namespace Database\Seeders;

use App\Models\ContactInformation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first();

        if ($admin) {
            ContactInformation::factory()->create([
                'user_id' => $admin->id,
                'address' => '2, Allen Avenue, Ikeja Lagos State, Nigeria.',
                'phone' => '+2348125449478',
                'email' => 'info@simbrella.com',
                'facebook_link' => 'https://facebook.com/simbrella',
                'instagram_link' => 'https://instagram.com/simbrella',
                'linkedin_link' => 'https://linkedin.com/in/simbrella',
                'twitter_link' => 'https://twitter.com/simbrella',
            ]);
        }
    }
}
