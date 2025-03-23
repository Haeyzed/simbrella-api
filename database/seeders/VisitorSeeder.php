<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();

        // Common browsers and their user agents
        $browsers = [
            'Chrome' => [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36'
            ],
            'Firefox' => [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:90.0) Gecko/20100101 Firefox/90.0'
            ],
            'Safari' => [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1'
            ],
            'Edge' => [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59'
            ],
            'Opera' => [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 OPR/77.0.4054.277'
            ]
        ];

        // Common devices
        $devices = ['Desktop', 'Mobile', 'Tablet', 'iPhone', 'Android', 'iPad'];

        // Common pages
        $pages = [
            '/',
            '/about',
            '/contact',
            '/blog',
            '/products',
            '/services',
            '/blog/how-to-get-started',
            '/blog/top-10-tips',
            '/products/featured',
            '/services/consulting'
        ];

        // Common referrers
        $referrers = [
            'https://google.com',
            'https://facebook.com',
            'https://twitter.com',
            'https://instagram.com',
            'https://linkedin.com',
            'https://youtube.com',
            'https://bing.com',
            'https://github.com',
            null // Direct traffic
        ];

        // Create visitors from the past week (for daily stats)
        $this->createVisitorsForPeriod(Carbon::now()->subDays(7), Carbon::now(), 500, $faker, $users, $browsers, $devices, $pages, $referrers);

        // Create visitors from the past month (for weekly stats)
        $this->createVisitorsForPeriod(Carbon::now()->subMonth(), Carbon::now()->subDays(7), 1000, $faker, $users, $browsers, $devices, $pages, $referrers);

        // Create visitors from the past 6 months (for monthly stats)
        $this->createVisitorsForPeriod(Carbon::now()->subMonths(6), Carbon::now()->subMonth(), 3000, $faker, $users, $browsers, $devices, $pages, $referrers);

        // Create a spike in traffic for a specific day to test visualization
        $this->createVisitorsForPeriod(Carbon::now()->subDays(3), Carbon::now()->subDays(2), 300, $faker, $users, $browsers, $devices, $pages, $referrers);

        $this->command->info('Visitor data seeded successfully!');
    }

    /**
     * Create visitors for a specific time period
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $count
     * @param Generator $faker
     * @param Collection $users
     * @param array $browsers
     * @param array $devices
     * @param array $pages
     * @param array $referrers
     * @return void
     */
    private function createVisitorsForPeriod(Carbon $startDate, Carbon $endDate, int $count, $faker, $users, $browsers, $devices, $pages, $referrers): void
    {
        $visitors = [];
        $period = $endDate->diffInSeconds($startDate);

        for ($i = 0; $i < $count; $i++) {
            // Randomly select a browser
            $browser = $faker->randomElement(array_keys($browsers));
            $userAgent = $faker->randomElement($browsers[$browser]);

            // Randomly decide if this visitor is a logged-in user
            $userId = $faker->boolean(30) ? $users->random()->id : null;

            // Create timestamp within the period
            $timestamp = $faker->dateTimeBetween($startDate, $endDate);

            $visitors[] = [
                'ip_address' => $faker->ipv4,
                'user_agent' => $userAgent,
                'browser' => $browser,
                'device' => $faker->randomElement($devices),
                'page_visited' => $faker->randomElement($pages),
                'referrer' => $faker->randomElement($referrers),
                'user_id' => $userId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            // Insert in batches of 100 to avoid memory issues
            if (count($visitors) >= 100) {
                Visitor::query()->insert($visitors);
                $visitors = [];
            }
        }

        // Insert any remaining records
        if (count($visitors) > 0) {
            Visitor::query()->insert($visitors);
        }
    }
}
