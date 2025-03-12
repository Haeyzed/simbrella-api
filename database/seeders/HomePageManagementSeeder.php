<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\ClientSection;
use App\Models\HeroImage;
use App\Models\HeroSection;
use App\Models\ProductSection;
use App\Models\ServiceSection;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HomePageManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure storage directories exist
        Storage::makeDirectory('public/hero/images');
        Storage::makeDirectory('public/services');

        $admin = User::query()->where('email', 'admin@example.com')->first();

        if ($admin) {
            // Create Hero Section with images
            $heroes = HeroSection::factory()
                ->count(5)
                ->create()
                ->each(function ($hero) {
                    // Create 1-5 related images for each hero section
                    HeroImage::factory()
                        ->count(rand(1, 5))
                        ->create(['hero_section_id' => $hero->id]);
                });

            // Create Services
            ServiceSection::factory()
                ->count(4)
                ->create([
                    'user_id' => $admin->id,
                    'title_short' => function (array $attributes) {
                        return substr($attributes['title'], 0, 30);
                    },
                    'summary_short' => function (array $attributes) {
                        return substr($attributes['summary'], 0, 100) . '...';
                    },
                    'image_path' => 'services/service-' . rand(1, 10) . '.jpg',
                    'icon_path' => 'services/service-' . rand(1, 10) . '.jpg',
                ]);

            // Create About Section
            AboutSection::factory()
                ->create(['user_id' => $admin->id]);

            // Create Products
            ProductSection::factory()
                ->count(6)
                ->create(['user_id' => $admin->id]);

            // Create Clients with Case Studies
            $clients = ClientSection::factory()
                ->count(6)
                ->create(['user_id' => $admin->id]);

            // Create Case Studies for each client
            $clients->each(function ($client) use ($admin) {
                $client->caseStudy()->create([
                    'company_name' => $client->company_name,
                    'subtitle' => 'Success Story',
                    'description' => 'Case study description...',
                    'user_id' => $admin->id,
                ]);
            });
        }
    }
}
