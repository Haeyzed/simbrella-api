<?php

namespace App\Services;

use App\Enums\BlogPostStatusEnum;
use App\Enums\SectionStatusEnum;
use App\Models\BlogPost;
use App\Models\ProductSection;
use App\Models\ServiceSection;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Service
 *
 * Provides methods to retrieve and process dashboard data.
 *
 * @package App\Services
 */
class DashboardService
{
    /**
     * Get summary statistics for the dashboard
     *
     * @return array<string, array<string, int>>
     */
    public function getSummaryStats(): array
    {
        return [
            'products' => [
                'total' => ProductSection::query()->count(),
                'published' => ProductSection::query()->where('status', SectionStatusEnum::PUBLISHED->value)->count(),
                'draft' => ProductSection::query()->where('status', SectionStatusEnum::DRAFT->value)->count(),
            ],
            'blogs' => [
                'total' => BlogPost::query()->count(),
                'published' => BlogPost::query()->where('status', BlogPostStatusEnum::PUBLISHED->value)->count(),
                'draft' => BlogPost::query()->where('status', BlogPostStatusEnum::DRAFT->value)->count(),
            ],
            'services' => [
                'total' => ServiceSection::query()->count(),
                'published' => ServiceSection::query()->where('status', SectionStatusEnum::PUBLISHED->value)->count(),
                'draft' => ServiceSection::query()->where('status', SectionStatusEnum::DRAFT->value)->count(),
            ],
        ];
    }

    /**
     * Get visitor statistics
     *
     * @param string $period daily|weekly|monthly
     * @return array<string, mixed>
     */
    public function getVisitorStats(string $period = 'daily'): array
    {
        $now = Carbon::now();
        $startDate = null;

        switch ($period) {
            case 'daily':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                break;
            case 'weekly':
                $startDate = $now->copy()->subWeeks(4)->startOfWeek();
                break;
            case 'monthly':
                $startDate = $now->copy()->subMonths(6)->startOfMonth();
                break;
        }

        // Get all visitors in the period
        $visitors = Visitor::query()
            ->where('created_at', '>=', $startDate)
            ->get();

        // Process the data in PHP instead of SQL to avoid GROUP BY issues
        $visitorsByPeriod = [];
        $visitorsByBrowser = [];

        foreach ($visitors as $visitor) {
            // Format the date based on period
            if ($period === 'daily') {
                $key = $visitor->created_at->format('l'); // Day name
            } elseif ($period === 'weekly') {
                $key = 'Week ' . $visitor->created_at->format('W'); // Week number
            } else {
                $key = $visitor->created_at->format('F'); // Month name
            }

            // Count by period
            if (!isset($visitorsByPeriod[$key])) {
                $visitorsByPeriod[$key] = 0;
            }
            $visitorsByPeriod[$key]++;

            // Count by browser
            if (!isset($visitorsByBrowser[$visitor->browser])) {
                $visitorsByBrowser[$visitor->browser] = 0;
            }
            $visitorsByBrowser[$visitor->browser]++;
        }

        // Sort the periods chronologically
        if ($period === 'daily') {
            // Sort days of week
            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $sorted = [];
            foreach ($daysOfWeek as $day) {
                if (isset($visitorsByPeriod[$day])) {
                    $sorted[$day] = $visitorsByPeriod[$day];
                }
            }
            $visitorsByPeriod = $sorted;
        }

        // Sort browsers by count (descending)
        arsort($visitorsByBrowser);

        $totalVisitors = count($visitors);

        return [
            'total' => $totalVisitors,
            'by_period' => $visitorsByPeriod,
            'by_browser' => $visitorsByBrowser,
            'period' => $period,
        ];
    }

    /**
     * Get top blog posts
     *
     * @param int $limit
     * @return Collection
     */
    public function getTopBlogPosts(int $limit = 3): Collection
    {
        return BlogPost::query()->where('status', BlogPostStatusEnum::PUBLISHED->value)
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity
     *
     * @param int $limit
     * @return array<int, array<string, mixed>>
     */
    public function getRecentActivity(int $limit = 3): array
    {
        // Get recent blog posts
        $recentBlogs = BlogPost::query()->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($blog) {
                return [
                    'type' => 'blog',
                    'title' => $blog->title,
                    'subtitle' => $blog->subtitle,
                    'image' => $blog->featured_image,
                    'updated_at' => $blog->updated_at,
                    'formatted_updated_at' => $blog->updated_at ? $blog->updated_at->diffForHumans() : null,
                ];
            });

        // Get recent products
        $recentProducts = ProductSection::query()->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'title' => $product->title,
                    'summary' => $product->summary,
                    'image' => $product->image,
                    'updated_at' => $product->updated_at,
                    'formatted_updated_at' => $product->updated_at ? $product->updated_at->diffForHumans() : null,
                ];
            });

        // Get recent services
        $recentServices = ServiceSection::query()->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($service) {
                return [
                    'type' => 'service',
                    'title' => $service->title,
                    'summary_short' => $service->summary_short,
                    'image' => $service->image,
                    'updated_at' => $service->updated_at,
                    'formatted_updated_at' => $service->updated_at ? $service->updated_at->diffForHumans() : null,
                ];
            });

        // Combine and sort by updated_at
        $allActivity = $recentBlogs->concat($recentProducts)->concat($recentServices)
            ->sortByDesc('updated_at')
            ->values()
            ->take($limit);

        return $allActivity->toArray();
    }

    /**
     * Get detailed statistics for a specific content type
     *
     * @param string $type products|blogs|services
     * @return array<string, mixed>
     */
    public function getDetailedStats(string $type): array
    {
        $model = null;
        $dateField = 'created_at';

        switch ($type) {
            case 'products':
                $model = new ProductSection();
                break;
            case 'blogs':
                $model = new BlogPost();
                break;
            case 'services':
                $model = new ServiceSection();
                break;
            default:
                return [];
        }

        // Get counts by month
        $countsByMonth = $model::query()->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get published vs draft
        $publishedVsDraft = [
            'published' => $model::query()->where('status', SectionStatusEnum::PUBLISHED->value)->count(),
            'draft' => $model::query()->where('status', SectionStatusEnum::DRAFT->value)->count(),
        ];

        // Get recent items
        $recentItems = $model::query()->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'type' => $type,
            'counts_by_month' => $countsByMonth,
            'published_vs_draft' => $publishedVsDraft,
            'recent_items' => $recentItems,
        ];
    }
}
