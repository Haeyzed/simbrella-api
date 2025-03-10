<?php

namespace App\Http\Resources;

use App\Models\ClientSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClientSectionResource
 *
 * Represents a client section resource.
 *
 * @package App\Http\Resources
 *
 * @property ClientSection $resource
 */
class ClientSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming request instance.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the client section.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The company name associated with the client section.
             *
             * @var string $company_name
             * @example "Softmax Technologies"
             */
            'company_name' => $this->company_name,

            /**
             * The logo path of the client section.
             *
             * @var string|null $logo_path
             * @example "uploads/logos/client.png"
             */
            'logo_path' => $this->logo_path,

            /**
             * The full logo URL.
             *
             * @var string|null $logo_url
             * @example "https://example.com/uploads/logos/client.png"
             */
            'logo_url' => $this->logo_url,

            /**
             * The display order of the client section.
             *
             * @var int $order
             * @example 1
             */
            'order' => $this->order,

            /**
             * The status of the client section.
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
             * The unique identifier of the user associated with the client section.
             *
             * @var int|null $user_id
             * @example 5
             */
            'user_id' => $this->user_id,

            /**
             * The user details if the relationship is loaded.
             *
             * @var array|null $user
             * @example {"id": 5, "name": "John Doe", "email": "john@example.com"}
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
             * The case study details if the relationship is loaded.
             *
             * @var CaseStudySectionResource|null $case_study
             */
            'case_study' => new CaseStudySectionResource($this->whenLoaded('caseStudy')),

            /**
             * Indicates whether a case study exists for this client section.
             *
             * @var bool $has_case_study
             * @example true
             */
            'has_case_study' => $this->caseStudy()->exists(),

            /**
             * The creation timestamp of the client section.
             *
             * @var string|null $created_at
             * @example "2024-03-04 12:30:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp of the client section.
             *
             * @var string|null $updated_at
             * @example "2024-03-05 15:45:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp of the client section (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example "2024-03-06 10:00:00"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
