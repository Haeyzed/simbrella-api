<?php

namespace App\Http\Resources;

use App\Models\ServiceSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ServiceSectionResource
 *
 * Represents a service section resource.
 *
 * @package App\Http\Resources
 *
 * @property ServiceSection $resource
 */
class ServiceSectionResource extends JsonResource
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
             * The unique identifier of the service section.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The title of the service section.
             *
             * @var string $title
             * @example "Web Development"
             */
            'title' => $this->title,

            /**
             * The short title of the service section.
             *
             * @var string|null $title_short
             * @example "Web Dev"
             */
            'title_short' => $this->title_short,

            /**
             * A detailed summary of the service section.
             *
             * @var string $summary
             * @example "We provide top-notch web development services."
             */
            'summary' => $this->summary,

            /**
             * A brief summary of the service section.
             *
             * @var string|null $summary_short
             * @example "Top-notch web development."
             */
            'summary_short' => $this->summary_short,

            /**
             * The icon representing the service section.
             *
             * @var string $icon
             * @example "fa-solid fa-code"
             */
            'icon' => $this->icon,

            /**
             * The image path of the service section.
             *
             * @var string|null $image_path
             * @example "services/web-dev-123.jpg"
             */
            'image_path' => $this->image_path,

            /**
             * The image URL of the service section.
             *
             * @var string|null $image_url
             * @example "https://example.com/storage/services/web-dev-123.jpg"
             */
            'image_url' => $this->image_url,

            /**
             * The display order of the service section.
             *
             * @var int|null $order
             * @example 2
             */
            'order' => $this->order,

            /**
             * The status of the service section.
             *
             * @var string $status
             * @example active
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
             * The ID of the user who created or manages the service section.
             *
             * @var int $user_id
             * @example 3
             */
            'user_id' => $this->user_id,

            /**
             * The user associated with the service section.
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
             * The creation timestamp of the service section.
             *
             * @var string|null $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the service section.
             *
             * @var string|null $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp of the service section (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example "2024-03-06 10:00:00"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
