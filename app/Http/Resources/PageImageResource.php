<?php

namespace App\Http\Resources;

use App\Models\PageImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PageImageResource
 *
 * Represents a page image resource.
 *
 * @package App\Http\Resources
 *
 * @property PageImage $resource
 */
class PageImageResource extends JsonResource
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
             * The unique identifier of the page image.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The type of the page image.
             *
             * @var string $type
             * @example "service_page"
             */
            'type' => $this->type,

            /**
             * The display name of the page image type.
             *
             * @var string $type_label
             * @example "Service Page"
             */
            'type_label' => $this->type ? $this->type->label() : null,

            /**
             * The filename of the image.
             *
             * @var string $image_path
             * @example "service-page-banner.jpg"
             */
            'image_path' => $this->image_path,

            /**
             * The full URL of the image.
             *
             * @var string $image_url
             * @example "https://example.com/storage/service-page-banner.jpg"
             */
            'image_url' => $this->image_url,

            /**
             * The title of the image.
             *
             * @var string|null $title
             * @example "Service Page Banner"
             */
            'title' => $this->title,

            /**
             * The alt text for the image.
             *
             * @var string|null $alt_text
             * @example "Our professional services"
             */
            'alt_text' => $this->alt_text,

            /**
             * The description of the image.
             *
             * @var string|null $description
             * @example "Banner image for the services page"
             */
            'description' => $this->description,

            /**
             * The creation timestamp of the image.
             *
             * @var string $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the image.
             *
             * @var string $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The formatted creation date of the image.
             *
             * @var string|null $formatted_created_at
             * @example "March 4, 2024"
             */
            'formatted_created_at' => $this->created_at ? $this->created_at->format('F j, Y') : null,

            /**
             * The formatted last update date of the image.
             *
             * @var string|null $formatted_updated_at
             * @example "March 5, 2024"
             */
            'formatted_updated_at' => $this->updated_at ? $this->updated_at->format('F j, Y') : null,
        ];
    }
}