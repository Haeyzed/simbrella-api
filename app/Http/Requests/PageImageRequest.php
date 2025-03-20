<?php

namespace App\Http\Requests;

use App\Enums\PageImageTypeEnum;
use Illuminate\Validation\Rules\Enum;

class PageImageRequest extends BaseRequest
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
             * The type of the page image.
             *
             * @var PageImageTypeEnum $type
             * @example \App\Enums\PageImageTypeEnum::SERVICE_PAGE
             */
            'type' => ['required', new Enum(PageImageTypeEnum::class)],

            /**
             * The image file to upload.
             *
             * This field is required for new uploads and optional for updates.
             *
             * @var \Illuminate\Http\UploadedFile|null $image
             */
            'image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],

            /**
             * The title of the image.
             *
             * @var string|null $title
             * @example "Service Page Banner"
             */
            'title' => ['nullable', 'string', 'max:255'],

            /**
             * The alt text for the image.
             *
             * @var string|null $alt_text
             * @example "Our professional services"
             */
            'alt_text' => ['nullable', 'string', 'max:255'],

            /**
             * The description of the image.
             *
             * @var string|null $description
             * @example "Banner image for the services page"
             */
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'Image type',
            'image' => 'Image file',
            'title' => 'Image title',
            'alt_text' => 'Alternative text',
            'description' => 'Image description',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.max' => 'The image must not be larger than 2MB.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'type.required' => 'Please select an image type.',
        ];
    }
}