<?php

namespace App\Http\Requests;

use App\Enums\CareerStatusEnum;
use Illuminate\Validation\Rules\Enum;

class CareerRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The job title.
             *
             * @var string $title
             * @example "Software Engineer"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The sub-title for the job posting.
             *
             * @var string|null $subtitle
             * @example "Senior Level"
             */
            'subtitle' => ['nullable', 'string'],

            /**
             * The job description.
             *
             * @var string $description
             * @example "We are looking for a skilled developer..."
             */
            'description' => ['required', 'string'],

            /**
             * The job location.
             *
             * @var string $location
             * @example "New York, USA"
             */
            'location' => ['required', 'string', 'max:255'],

            /**
             * The work format.
             *
             * @var string $format
             * @example "remote"
             */
            'format' => ['required', 'string', 'in:remote,onsite,hybrid'],

            /**
             * The department name.
             *
             * @var string|null $department
             * @example "Engineering"
             */
            'department' => ['nullable', 'string', 'max:255'],

            /**
             * The employment type.
             *
             * @var string|null $employment_type
             * @example "full-time"
             */
            'employment_type' => ['nullable', 'string', 'in:full-time,part-time,contract'],

            /**
             * The minimum salary.
             *
             * @var float|null $salary_min
             * @example 50000
             */
            'salary_min' => ['nullable', 'numeric', 'min:0'],

            /**
             * The maximum salary.
             *
             * @var float|null $salary_max
             * @example 100000
             */
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],

            /**
             * The salary currency.
             *
             * @var string|null $currency
             * @example "USD"
             */
            'currency' => ['nullable', 'string', 'size:3'],

            /**
             * The application email.
             *
             * @var string $application_email
             * @example "jobs@example.com"
             */
            'application_email' => ['required', 'email'],

            /**
             * The job requirements.
             *
             * @var string|null $requirements
             * @example "Experience with PHP and Laravel."
             */
            'requirements' => ['nullable', 'string'],

            /**
             * The job benefits.
             *
             * @var string|null $benefits
             * @example "Health insurance, remote work."
             */
            'benefits' => ['nullable', 'string'],

            /**
             * The banner image for the job listing.
             *
             * @var string|null $banner_image
             * @example "job-banner.jpg"
             */
            'banner_image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],

            /**
             * The job status.
             *
             * @var string|null $status
             * @example "open"
             */
            'status' => ['sometimes', new Enum(CareerStatusEnum::class)],

            /**
             * The publishing date.
             *
             * @var string|null $published_at
             * @example "2024-01-01"
             */
            'published_at' => ['nullable', 'date'],

            /**
             * The expiry date.
             *
             * @var string|null $expires_at
             * @example "2024-12-31"
             */
            'expires_at' => ['nullable', 'date', 'after:published_at'],
        ];
    }
}
