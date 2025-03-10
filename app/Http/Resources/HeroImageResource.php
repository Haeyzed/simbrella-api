<?php

namespace App\Http\Resources;

use App\Models\HeroImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class HeroImageResource
 *
 * Represents a hero image resource.
 *
 * @package App\Http\Resources
 *
 * @property HeroImage $resource
 */
class HeroImageResource extends JsonResource
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
             * The unique identifier of the hero image.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The ID of the associated hero section.
             *
             * @var int $hero_section_id
             * @example 5
             */
            'hero_section_id' => $this->hero_section_id,

            /**
             * The file path of the hero image.
             *
             * @var string $image_path
             * @example "images/hero/banner1.jpg"
             */
            'image_path' => $this->image_path,

            /**
             * The full URL of the hero image.
             *
             * @var string $image_url
             * @example "https://example.com/storage/images/hero/banner1.jpg"
             */
            'image_url' => $this->image_url,

            /**
             * The display order of the hero image.
             *
             * @var int|null $order
             * @example 2
             */
            'order' => $this->order,

            /**
             * The creation timestamp of the hero image.
             *
             * @var string|null $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the hero image.
             *
             * @var string|null $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
