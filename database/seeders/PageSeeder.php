<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample page data
        $pages = [
            [
                'title' => 'Home',
                'subtitle' => 'Welcome to our website',
                'content' => '<h1>Welcome to Our Company</h1><p>We are dedicated to providing the best services to our clients.</p><p>Explore our website to learn more about what we offer.</p>',
                'meta_title' => 'Home | Our Company',
                'meta_description' => 'Welcome to our company website. We provide top-quality services to clients worldwide.',
                'meta_keywords' => 'home, company, services, welcome',
                'is_published' => true,
                'order' => 1,
            ],
            [
                'title' => 'About Us',
                'subtitle' => 'Learn more about our company',
                'content' => '<h1>About Our Company</h1><p>Founded in 2010, our company has been at the forefront of innovation in our industry.</p><p>Our team of experts is dedicated to delivering exceptional results for our clients.</p><h2>Our Mission</h2><p>To provide high-quality services that exceed our clients\' expectations.</p><h2>Our Vision</h2><p>To become the leading provider of solutions in our industry.</p>',
                'meta_title' => 'About Us | Our Company',
                'meta_description' => 'Learn about our company history, mission, and values. Discover what makes us unique.',
                'meta_keywords' => 'about us, company history, mission, vision, values',
                'is_published' => true,
                'order' => 2,
            ],
            [
                'title' => 'Services',
                'subtitle' => 'Explore our range of services',
                'content' => '<h1>Our Services</h1><p>We offer a wide range of services to meet your needs.</p><h2>Service 1</h2><p>Description of service 1.</p><h2>Service 2</h2><p>Description of service 2.</p><h2>Service 3</h2><p>Description of service 3.</p>',
                'meta_title' => 'Services | Our Company',
                'meta_description' => 'Explore our comprehensive range of services designed to meet your specific needs.',
                'meta_keywords' => 'services, solutions, offerings',
                'is_published' => true,
                'order' => 3,
            ],
            [
                'title' => 'Careers',
                'subtitle' => 'Join our team',
                'content' => '<h1>Career Opportunities</h1><p>Join our dynamic team and grow your career with us.</p><h2>Current Openings</h2><p>Check back regularly for new job postings.</p><h2>Benefits</h2><p>We offer competitive salaries, health insurance, and professional development opportunities.</p>',
                'meta_title' => 'Careers | Our Company',
                'meta_description' => 'Explore career opportunities at our company. Join our team and grow professionally.',
                'meta_keywords' => 'careers, jobs, employment, opportunities',
                'is_published' => true,
                'order' => 4,
            ],
            [
                'title' => 'Contact Us',
                'subtitle' => 'Get in touch with us',
                'content' => '<h1>Contact Us</h1><p>We\'d love to hear from you. Reach out to us using the information below.</p><h2>Address</h2><p>123 Main Street, City, Country</p><h2>Phone</h2><p>+1 234 567 8900</p><h2>Email</h2><p>info@example.com</p>',
                'meta_title' => 'Contact Us | Our Company',
                'meta_description' => 'Get in touch with our team. We\'re here to answer your questions and address your concerns.',
                'meta_keywords' => 'contact, get in touch, address, phone, email',
                'is_published' => true,
                'order' => 5,
            ],
            [
                'title' => 'Privacy Policy',
                'subtitle' => 'Our commitment to your privacy',
                'content' => '<h1>Privacy Policy</h1><p>This privacy policy outlines how we collect, use, and protect your personal information.</p><h2>Information Collection</h2><p>We collect information to provide and improve our services.</p><h2>Information Usage</h2><p>We use your information to process transactions, maintain your account, and provide customer support.</p>',
                'meta_title' => 'Privacy Policy | Our Company',
                'meta_description' => 'Read our privacy policy to understand how we collect, use, and protect your personal information.',
                'meta_keywords' => 'privacy policy, data protection, personal information',
                'is_published' => true,
                'order' => 6,
            ],
            [
                'title' => 'Terms of Service',
                'subtitle' => 'Terms and conditions for using our services',
                'content' => '<h1>Terms of Service</h1><p>By accessing our website, you agree to these terms and conditions.</p><h2>Use License</h2><p>Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.</p><h2>Disclaimer</h2><p>The materials on our website are provided on an \'as is\' basis.</p>',
                'meta_title' => 'Terms of Service | Our Company',
                'meta_description' => 'Read our terms of service to understand the conditions for using our website and services.',
                'meta_keywords' => 'terms of service, terms and conditions, legal',
                'is_published' => true,
                'order' => 7,
            ],
            [
                'title' => 'FAQ',
                'subtitle' => 'Frequently Asked Questions',
                'content' => '<h1>Frequently Asked Questions</h1><p>Find answers to common questions about our services.</p><h2>Question 1</h2><p>Answer to question 1.</p><h2>Question 2</h2><p>Answer to question 2.</p><h2>Question 3</h2><p>Answer to question 3.</p>',
                'meta_title' => 'FAQ | Our Company',
                'meta_description' => 'Find answers to frequently asked questions about our services and policies.',
                'meta_keywords' => 'FAQ, questions, answers, help',
                'is_published' => false,
                'order' => 8,
            ],
        ];

        // Create pages
        foreach ($pages as $pageData) {
            // Generate slug if not provided
            if (!isset($pageData['slug'])) {
                $pageData['slug'] = Str::slug($pageData['title']);
            }

            // Check if page already exists
            $exists = Page::where('slug', $pageData['slug'])->exists();
            
            if (!$exists) {
                Page::create($pageData);
            }
        }

        // Create some draft pages
        for ($i = 1; $i <= 3; $i++) {
            $title = 'Draft Page ' . $i;
            $slug = Str::slug($title);
            
            // Check if page already exists
            $exists = Page::where('slug', $slug)->exists();
            
            if (!$exists) {
                Page::create([
                    'title' => $title,
                    'slug' => $slug,
                    'subtitle' => 'This is a draft page',
                    'content' => '<h1>' . $title . '</h1><p>This page is still under construction.</p>',
                    'meta_title' => $title . ' | Our Company',
                    'meta_description' => 'This page is still under construction.',
                    'meta_keywords' => 'draft, under construction',
                    'is_published' => false,
                    'order' => 10 + $i,
                ]);
            }
        }
    }
}