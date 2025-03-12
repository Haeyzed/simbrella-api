<?php

namespace App\Http\Requests;

use App\Enums\SectionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class HeroSectionRequest extends BaseRequest
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
             * The title of the hero section.
             *
             * @var string $title
             * @example "Welcome to Our Website"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The subtitle of the hero section.
             *
             * @var string|null $subtitle
             * @example "Your success starts here."
             */
            'subtitle' => ['nullable', 'string'],

            /**
             * The status of the hero section.
             *
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', new Enum(SectionStatusEnum::class)],

            /**
             * The images for the hero section.
             *
             * @var array $images
             * @example ["image1.jpg", "image2.png", "image3.gif"]
             */
            'images' => ['required', 'array', 'min:3'],

            /**
             * Each image file in the hero section.
             *
             * @var string $images.*
             * @example "hero-banner.jpg"
             */
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.min' => 'At least 3 images are required for the hero section.',
            'images.*.max' => 'Each image must not be larger than 500KB.',
        ];
    }
}
