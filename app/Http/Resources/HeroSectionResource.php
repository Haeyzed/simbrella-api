<?php

namespace App\Http\Resources;

use App\Models\HeroSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class HeroSectionResource
 *
 * Represents a hero section resource.
 *
 * @package App\Http\Resources
 *
 * @property HeroSection $resource
 */
class HeroSectionResource extends JsonResource
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
             * The unique identifier of the hero section.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The title of the hero section.
             *
             * @var string $title
             * @example "Welcome to Our Platform"
             */
            'title' => $this->title,

            /**
             * The subtitle of the hero section.
             *
             * @var string|null $subtitle
             * @example "Discover amazing features and services."
             */
            'subtitle' => $this->subtitle,

            /**
             * The status of the hero section.
             *
             * @var string $status
             * @example "published"
             */
            'status' => $this->status,

            /**
             * The label of the hero section status.
             *
             * @var string|null $status_label
             * @example "Active"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The color code associated with the hero section status.
             *
             * @var string|null $status_color
             * @example "#28a745"
             */
            'status_color' => $this->status ? $this->status->color() : null,

            /**
             * The user ID of the hero section creator.
             *
             * @var int $user_id
             * @example 3
             */
            'user_id' => $this->user_id,

            /**
             * The user details of the hero section creator.
             *
             * @var array|null $user
             * @example {"id": 3, "name": "John Doe", "email": "johndoe@example.com"}
             */
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'full_name' => $this->user->full_name,
                    'email' => $this->user->email,
                ];
            }),

            /**
             * The images related to the hero section.
             *
             * @var array $images
             * @example [{"id": 1, "url": "https://example.com/image1.jpg"}, {"id": 2, "url": "https://example.com/image2.jpg"}]
             */
            'images' => HeroImageResource::collection($this->whenLoaded('images')),

            /**
             * The total count of related images.
             *
             * @var int $images_count
             * @example 5
             */
            'images_count' => $this->whenCounted('images'),

            /**
             * The creation timestamp of the hero section.
             *
             * @var string|null $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the hero section.
             *
             * @var string|null $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp of the hero section (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example null
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
