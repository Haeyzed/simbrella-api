<?php

namespace App\Http\Requests;

use App\Enums\SectionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class CaseStudySectionRequest extends BaseRequest
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
             * The ID of the related client section.
             *
             * @var int $client_section_id
             * @example 1
             */
            'client_section_id' => ['required', 'exists:client_sections,id'],

            /**
             * The banner image for the case study.
             *
             * @var string|null $banner_image
             * @example "case-study-banner.jpg"
             */
            'banner_image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:500' // 500KB
            ],

            /**
             * The name of the client company.
             *
             * @var string $company_name
             * @example "Acme Inc."
             */
            'company_name' => ['required', 'string', 'max:255'],

            /**
             * The subtitle of the case study.
             *
             * @var string|null $subtitle
             * @example "Improving Efficiency with Automation"
             */
            'subtitle' => ['nullable', 'string', 'max:255'],

            /**
             * The detailed description of the case study.
             *
             * @var string $description
             * @example "This case study explores how Acme Inc. improved efficiency."
             */
            'description' => ['required', 'string'],

            /**
             * The challenge faced by the client.
             *
             * @var string|null $challenge
             * @example "Acme Inc. struggled with manual data processing."
             */
            'challenge' => ['nullable', 'string'],

            /**
             * The solution provided to the client.
             *
             * @var string|null $solution
             * @example "We implemented an automated workflow system."
             */
            'solution' => ['nullable', 'string'],

            /**
             * The results achieved from the solution.
             *
             * @var string|null $results
             * @example "Acme Inc. saw a 40% increase in efficiency."
             */
            'results' => ['nullable', 'string'],

            /**
             * The status of the case study section.
             *
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', new Enum(SectionStatusEnum::class)],
        ];
    }
}
