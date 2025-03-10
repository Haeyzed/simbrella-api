<?php

namespace App\Http\Resources;

use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CareerResource
 *
 * @package App\Http\Resources
 *
 * @property Career $resource
 */
class CareerResource extends JsonResource
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
             * The unique identifier for the career.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The title of the career position.
             *
             * @var string $title
             * @example "Software Engineer"
             */
            'title' => $this->title,

            /**
             * The subtitle or short description of the career position.
             *
             * @var string|null $subtitle
             * @example "Backend Developer Role"
             */
            'subtitle' => $this->subtitle,

            /**
             * The detailed job description.
             *
             * @var string $description
             * @example "This role involves building APIs and microservices."
             */
            'description' => $this->description,

            /**
             * The job location.
             *
             * @var string $location
             * @example "New York, USA"
             */
            'location' => $this->location,

            /**
             * The job format (e.g., remote, hybrid, on-site).
             *
             * @var string $format
             * @example "Remote"
             */
            'format' => $this->format,

            /**
             * The department the job belongs to.
             *
             * @var string $department
             * @example "Engineering"
             */
            'department' => $this->department,

            /**
             * The type of employment (e.g., full-time, part-time, contract).
             *
             * @var string $employment_type
             * @example "Full-time"
             */
            'employment_type' => $this->employment_type,

            /**
             * The minimum salary offered.
             *
             * @var float|null $salary_min
             * @example 50000
             */
            'salary_min' => $this->salary_min,

            /**
             * The maximum salary offered.
             *
             * @var float|null $salary_max
             * @example 80000
             */
            'salary_max' => $this->salary_max,

            /**
             * The salary range for the position.
             *
             * @var string|null $salary_range
             * @example "$50,000 - $80,000 per year"
             */
            'salary_range' => $this->salary_range,

            /**
             * The currency in which the salary is offered.
             *
             * @var string $currency
             * @example "USD"
             */
            'currency' => $this->currency,

            /**
             * The email address for receiving applications.
             *
             * @var string $application_email
             * @example "careers@company.com"
             */
            'application_email' => $this->application_email,

            /**
             * The job requirements.
             *
             * @var array|null $requirements
             * @example ["3+ years experience", "Proficiency in Laravel"]
             */
            'requirements' => $this->requirements,

            /**
             * The benefits provided for this position.
             *
             * @var array|null $benefits
             * @example ["Health insurance", "Stock options"]
             */
            'benefits' => $this->benefits,

            /**
             * The banner image path for the job post.
             *
             * @var string|null $banner_image
             * @example "/uploads/careers/banner.jpg"
             */
            'banner_image' => $this->banner_image,

            /**
             * The full URL of the banner image.
             *
             * @var string|null $banner_image_url
             * @example "https://company.com/uploads/careers/banner.jpg"
             */
            'banner_image_url' => $this->banner_image_url,

            /**
             * The status of the job post.
             *
             * @var string $status
             * @example "active"
             */
            'status' => $this->status,

            /**
             * The label for the job status.
             *
             * @var string $status_label
             * @example "Active"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The color associated with the job status.
             *
             * @var string $status_color
             * @example "green"
             */
            'status_color' => $this->status ? $this->status->color() : null,

            /**
             * The timestamp when the job was published.
             *
             * @var string|null $published_at
             * @example "2023-06-15T10:00:00Z"
             */
            'published_at' => $this->published_at,

            /**
             * The timestamp when the job expires.
             *
             * @var string|null $expires_at
             * @example "2023-12-31T23:59:59Z"
             */
            'expires_at' => $this->expires_at,

            /**
             * The user ID of the job poster.
             *
             * @var int|null $user_id
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * The user details of the job poster.
             *
             * @var array|null $user
             * @example {"id": 1, "name": "John Doe", "email": "john.doe@example.com"}
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
             * The timestamp when the job post was created.
             *
             * @var string|null $created_at
             * @example "2023-05-10T14:30:00Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the job post was last updated.
             *
             * @var string|null $updated_at
             * @example "2023-06-01T09:15:00Z"
             */
            'updated_at' => $this->updated_at,

            /**
             * The timestamp when the job post was deleted (soft delete).
             *
             * @var string|null $deleted_at
             * @example "2023-07-20T17:45:00Z"
             */
            'deleted_at' => $this->deleted_at,

            /**
             * The formatted date when the job post was created.
             *
             * @var string|null $formatted_created_at
             * @example "May 10, 2023"
             */
            'formatted_created_at' => $this->created_at?->format('F j, Y'),

            /**
             * The formatted date when the job post was last updated.
             *
             * @var string|null $formatted_updated_at
             * @example "June 1, 2023"
             */
            'formatted_updated_at' => $this->updated_at?->format('F j, Y'),

            /**
             * The formatted date when the job post was published.
             *
             * @var string|null $formatted_published_at
             * @example "June 15, 2023"
             */
            'formatted_published_at' => $this->published_at?->format('F j, Y'),

            /**
             * The formatted date when the job post expires.
             *
             * @var string|null $formatted_expires_at
             * @example "December 31, 2023"
             */
            'formatted_expires_at' => $this->expires_at?->format('F j, Y'),
        ];
    }
}
