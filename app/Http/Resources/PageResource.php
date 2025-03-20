<?php

namespace App\Http\Resources;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PageResource
 *
 * Represents a page resource.
 *
 * @package App\Http\Resources
 *
 * @property Page $resource
 */
class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the page.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The title of the page.
             *
             * @var string $title
             * @example "About Us"
             */
            'title' => $this->title,

            /**
             * The slug of the page.
             *
             * @var string $slug
             * @example "about-us"
             */
            'slug' => $this->slug,

            /**
             * The subtitle of the page.
             *
             * @var string|null $subtitle
             * @example "Learn more about our company"
             */
            'subtitle' => $this->subtitle,

            /**
             * The content of the page.
             *
             * @var string $content
             * @example "<p>Welcome to our company...</p>"
             */
            'content' => $this->content,

            /**
             * The meta title of the page.
             *
             * @var string|null $meta_title
             * @example "About Our Company | Example Inc."
             */
            'meta_title' => $this->meta_title,

            /**
             * The meta description of the page.
             *
             * @var string|null $meta_description
             * @example "Learn about our company history, mission, and values."
             */
            'meta_description' => $this->meta_description,

            /**
             * The meta keywords of the page.
             *
             * @var string|null $meta_keywords
             * @example "company, about us, history, mission, values"
             */
            'meta_keywords' => $this->meta_keywords,

            /**
             * Whether the page is published.
             *
             * @var bool $is_published
             * @example true
             */
            'is_published' => $this->is_published,

            /**
             * The order of the page.
             *
             * @var int|null $order
             * @example 1
             */
            'order' => $this->order,

            /**
             * The creation timestamp of the page.
             *
             * @var string $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the page.
             *
             * @var string $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp of the page (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example null
             */
            'deleted_at' => $this->deleted_at,

            /**
             * The formatted creation date of the page.
             *
             * @var string|null $formatted_created_at
             * @example "March 4, 2024"
             */
            'formatted_created_at' => $this->created_at ? $this->created_at->format('F j, Y') : null,

            /**
             * The formatted last update date of the page.
             *
             * @var string|null $formatted_updated_at
             * @example "March 5, 2024"
             */
            'formatted_updated_at' => $this->updated_at ? $this->updated_at->format('F j, Y') : null,
        ];
    }
}