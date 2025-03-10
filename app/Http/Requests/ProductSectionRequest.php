<?php

namespace App\Http\Requests;

use App\Enums\SectionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class ProductSectionRequest extends BaseRequest
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
             * The title of the product section.
             *
             * @var string $title
             * @example "Featured Products"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The summary description of the product section.
             *
             * @var string $summary
             * @example "Discover our top-selling products."
             */
            'summary' => ['required', 'string', 'max:500'],

            /**
             * The image representing the product section.
             *
             * @var string|null $image
             * @example "featured-products.jpg"
             */
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:500'
            ],

            /**
             * The display order of the product section.
             *
             * @var int|null $order
             * @example 1
             */
            'order' => ['sometimes', 'integer', 'min:0'],

            /**
             * The status of the product section.
             *
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', new Enum(SectionStatusEnum::class)],
        ];
    }
}
