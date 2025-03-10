<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\BlogPostImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure storage directories exist
        Storage::makeDirectory('public/blog/banners');
        Storage::makeDirectory('public/blog/images');

        // Get or create users for the blog posts
        $users = User::query()->take(3)->get();

        if ($users->isEmpty()) {
            $users = User::factory(3)->create();
        }

        // Create 20 blog posts with related images
        $users->each(function ($user) {
            BlogPost::factory(5)
                ->for($user)
                ->create()
                ->each(function ($post) {
                    // Create 1-5 related images for each post
                    $imageCount = rand(1, 5);
                    BlogPostImage::factory($imageCount)
                        ->for($post, 'blogPost')
                        ->create();
                });
        });

        // Create some specific posts with different statuses
        $admin = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first() ?? $users->first();

        // Create 5 published posts
        BlogPost::factory(5)
            ->published()
            ->for($admin)
            ->create()
            ->each(function ($post) {
                BlogPostImage::factory(rand(3, 5))
                    ->for($post, 'blogPost')
                    ->create();
            });

        // Create 3 draft posts
        BlogPost::factory(3)
            ->draft()
            ->for($admin)
            ->create();

        // Create 2 archived posts
        BlogPost::factory(2)
            ->archived()
            ->for($admin)
            ->create();
    }
}
