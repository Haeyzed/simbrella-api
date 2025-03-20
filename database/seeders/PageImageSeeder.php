<?php

namespace Database\Seeders;

use App\Enums\PageImageTypeEnum;
use App\Models\PageImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PageImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure storage directory exists
        Storage::makeDirectory('public/page_images');

        // Sample image data
        $pageImages = [
            [
                'type' => PageImageTypeEnum::SERVICE_PAGE->value,
                'title' => 'Service Page Banner',
                'alt_text' => 'Our professional services',
                'description' => 'Banner image for the services page',
                'image_path' => 'page_images/service-banner.jpg',
            ],
            [
                'type' => PageImageTypeEnum::ABOUT_PAGE->value,
                'title' => 'About Us Banner',
                'alt_text' => 'About our company',
                'description' => 'Banner image for the about page',
                'image_path' => 'page_images/about-banner.jpg',
            ],
            [
                'type' => PageImageTypeEnum::CAREER_PAGE->value,
                'title' => 'Careers Banner',
                'alt_text' => 'Join our team',
                'description' => 'Banner image for the careers page',
                'image_path' => 'page_images/career-banner.jpg',
            ],
            [
                'type' => PageImageTypeEnum::CONTACT_PAGE->value,
                'title' => 'Contact Us Banner',
                'alt_text' => 'Get in touch with us',
                'description' => 'Banner image for the contact page',
                'image_path' => 'page_images/contact-banner.jpg',
            ],
            [
                'type' => PageImageTypeEnum::LOGO->value,
                'title' => 'Company Logo',
                'alt_text' => 'Company Logo',
                'description' => 'Official logo of the company',
                'image_path' => 'page_images/logo.png',
            ],
            [
                'type' => PageImageTypeEnum::NIGERIAN_FLAG->value,
                'title' => 'Nigerian Flag',
                'alt_text' => 'Flag of Nigeria',
                'description' => 'Nigerian flag for the website',
                'image_path' => 'page_images/nigerian-flag.png',
            ],
        ];

        // Create page images
        foreach ($pageImages as $imageData) {
            // Check if image already exists
            $exists = PageImage::where('type', $imageData['type'])->exists();
            
            if (!$exists) {
                PageImage::create($imageData);
            }
        }

        // Create some additional images for each type
        foreach (PageImageTypeEnum::cases() as $type) {
            // Create 1-3 additional images for each type
            $count = rand(1, 3);
            
            for ($i = 1; $i <= $count; $i++) {
                PageImage::create([
                    'type' => $type->value,
                    'title' => $type->label() . ' Image ' . $i,
                    'alt_text' => 'Alternative image for ' . $type->label(),
                    'description' => 'Additional image for ' . $type->label(),
                    'image_path' => 'page_images/' . strtolower(str_replace('_', '-', $type->value)) . '-' . $i . '.jpg',
                ]);
            }
        }
    }
}