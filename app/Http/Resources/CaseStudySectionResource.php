<?php

namespace App\Http\Resources;

use App\Models\CaseStudySection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CaseStudySectionResource
 *
 * Represents a case study section resource.
 *
 * @package App\Http\Resources
 *
 * @property CaseStudySection $resource
 */
class CaseStudySectionResource extends JsonResource
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
             * The unique identifier of the case study section.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The ID of the related client section.
             *
             * @var int $client_section_id
             * @example 2
             */
            'client_section_id' => $this->client_section_id,

            /**
             * The banner image path.
             *
             * @var string|null $banner_image
             * @example "uploads/case-study/banner.jpg"
             */
            'banner_image' => $this->banner_image,

            /**
             * The full banner image URL.
             *
             * @var string|null $banner_image_url
             * @example "https://example.com/uploads/case-study/banner.jpg"
             */
            'banner_image_url' => $this->banner_image_url,

            /**
             * The name of the company associated with the case study.
             *
             * @var string $company_name
             * @example "Tech Solutions Ltd."
             */
            'company_name' => $this->company_name,

            /**
             * The subtitle of the case study.
             *
             * @var string|null $subtitle
             * @example "A Success Story in E-Commerce"
             */
            'subtitle' => $this->subtitle,

            /**
             * The description of the case study.
             *
             * @var string $description
             * @example "This case study outlines the challenges faced and solutions implemented."
             */
            'description' => $this->description,

            /**
             * The challenge described in the case study.
             *
             * @var string $challenge
             * @example "The company struggled with order management."
             */
            'challenge' => $this->challenge,

            /**
             * The solution provided in the case study.
             *
             * @var string $solution
             * @example "We developed a custom POS system."
             */
            'solution' => $this->solution,

            /**
             * The results achieved after implementing the solution.
             *
             * @var string $results
             * @example "Order processing time reduced by 50%."
             */
            'results' => $this->results,

            /**
             * The status of the case study section.
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
             * The ID of the user who created the case study.
             *
             * @var int $user_id
             * @example 3
             */
            'user_id' => $this->user_id,

            /**
             * The user who created the case study.
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
             * The client section associated with the case study.
             *
             * @var ClientSectionResource|null $client
             */
            'client' => new ClientSectionResource($this->whenLoaded('client')),

            /**
             * The creation timestamp of the case study section.
             *
             * @var string|null $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the case study section.
             *
             * @var string|null $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp of the case study section (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example "2024-03-06 10:00:00"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
