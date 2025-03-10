<?php

namespace App\Http\Resources;

use App\Models\ProductSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductSectionResource
 *
 * Represents a product section resource.
 *
 * @package App\Http\Resources
 *
 * @property ProductSection $resource
 */
class ProductSectionResource extends JsonResource
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
             * The unique identifier of the product section.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The title of the product section.
             *
             * @var string $title
             * @example "Featured Products"
             */
            'title' => $this->title,

            /**
             * A brief summary of the product section.
             *
             * @var string $summary
             * @example "Check out our top-selling products..."
             */
            'summary' => $this->summary,

            /**
             * The image path for the product section.
             *
             * @var string|null $image_path
             * @example "uploads/products/section.jpg"
             */
            'image_path' => $this->image_path,

            /**
             * The full image URL for the product section.
             *
             * @var string|null $image_url
             * @example "https://example.com/uploads/products/section.jpg"
             */
            'image_url' => $this->image_url,

            /**
             * The display order of the product section.
             *
             * @var int $order
             * @example 1
             */
            'order' => $this->order,

            /**
             * The status of the product section.
             *
             * @var int $status
             * @example 1
             */
            'status' => $this->status,

            /**
             * The human-readable status label.
             *
             * @var string|null $status_label
             * @example "Active"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The color representation of the status.
             *
             * @var string|null $status_color
             * @example "#28a745"
             */
            'status_color' => $this->status ? $this->status->color() : null,

            /**
             * The ID of the user who created or manages the product section.
             *
             * @var int $user_id
             * @example 3
             */
            'user_id' => $this->user_id,

            /**
             * The user associated with the product section.
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
             * The creation timestamp of the product section.
             *
             * @var string|null $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the product section.
             *
             * @var string|null $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp of the product section (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example "2024-03-06 10:00:00"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
