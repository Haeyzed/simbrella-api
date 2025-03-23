<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class DashboardResource
 *
 * Represents a dashboard data resource.
 *
 * @package App\Http\Resources
 */
class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Summary statistics for the dashboard.
             *
             * @var array|null $summary
             * @example {"products": {"total": 10, "published": 8, "draft": 2}, "blogs": {"total": 15, "published": 12, "draft": 3}, "services": {"total": 6, "published": 5, "draft": 1}}
             */
            'summary' => $this['summary'] ?? null,

            /**
             * Visitor statistics data.
             *
             * @var array|null $visitors
             * @example {"total": 6400, "by_period": {"Monday": 1800, "Tuesday": 3200, "Wednesday": 4100}, "by_browser": {"Chrome": 3200, "Firefox": 1600, "Safari": 1000, "Mobile Phone": 600}, "period": "daily"}
             */
            'visitors' => $this['visitors'] ?? null,

            /**
             * Top blog posts by views.
             *
             * @var array|null $top_blogs
             * @example [{"id": 1, "title": "Sample Blog Post", "views": 1500}]
             */
            'top_blogs' => BlogPostResource::collection($this->whenLoaded('top_blogs')),

            /**
             * Recent activity across the platform.
             *
             * @var array|null $recent_activity
             * @example [{"type": "blog", "title": "New Blog Post", "updated_at": "2024-03-23 14:30:00"}]
             */
            'recent_activity' => $this['recent_activity'] ?? null,

            /**
             * Detailed statistics for a specific content type.
             *
             * @var array|null $detailed_stats
             * @example {"type": "blogs", "counts_by_month": [{"year": 2024, "month": 3, "count": 5}], "published_vs_draft": {"published": 12, "draft": 3}}
             */
            'detailed_stats' => $this['detailed_stats'] ?? null,
        ];
    }
}
