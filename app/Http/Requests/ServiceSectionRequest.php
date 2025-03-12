<?php

namespace App\Http\Requests;

use App\Enums\SectionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class ServiceSectionRequest extends BaseRequest
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
             * The title of the service section.
             *
             * @var string $title
             * @example "Our Services"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The short title of the service section (for cards, previews, etc.).
             *
             * @var string|null $title_short
             * @example "Services"
             */
            'title_short' => ['nullable', 'string', 'max:100'],

            /**
             * A detailed summary of the service section.
             *
             * @var string $summary
             * @example "We offer a range of services tailored to your needs."
             */
            'summary' => ['required', 'string', 'max:1000'],

            /**
             * A brief summary of the service section (for cards, previews, etc.).
             *
             * @var string|null $summary_short
             * @example "Tailored services for your needs."
             */
            'summary_short' => ['nullable', 'string', 'max:200'],

            /**
             * The icon for the service section.
             *
             * @var file|null $icon
             */
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            /**
             * The image for the service section.
             *
             * @var file|null $image
             */
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            /**
             * The order position of the service section.
             *
             * @var int|null $order
             * @example 1
             */
            'order' => ['sometimes', 'integer', 'min:0'],

            /**
             * The status of the service section.
             *
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', new Enum(SectionStatusEnum::class)],
        ];
    }
}
