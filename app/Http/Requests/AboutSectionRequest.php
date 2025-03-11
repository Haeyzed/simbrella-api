<?php

namespace App\Http\Requests;

use App\Enums\SectionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class AboutSectionRequest extends BaseRequest
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
             * The title of the about section.
             *
             * @var string $title
             * @example "About Our Company"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The summary description of the about section.
             *
             * @var string $summary
             * @example "We are a leading provider of..."
             */
            'summary' => ['required', 'string', 'max:500'],

            /**
             * The image representing the about section.
             *
             * @var string|null $image
             * @example "about-section.jpg"
             */
            'image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],

            /**
             * The status of the about section.
             *
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', new Enum(SectionStatusEnum::class)],
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.min' => 'The image must be at least 500KB.',
            'image.max' => 'The image must not be larger than 2MB.',
        ];
    }
}
