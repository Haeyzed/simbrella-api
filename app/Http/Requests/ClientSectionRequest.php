<?php

namespace App\Http\Requests;

use App\Enums\SectionStatusEnum;
use Illuminate\Validation\Rules\Enum;

class ClientSectionRequest extends BaseRequest
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
             * The name of the client company.
             *
             * @var string $company_name
             * @example "Acme Inc."
             */
            'company_name' => ['required', 'string', 'max:255'],

            /**
             * The logo image of the client company.
             *
             * @var string|null $logo
             * @example "client-logo.jpg"
             */
            'logo' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:500' // 500KB
            ],

            /**
             * The display order of the client section.
             *
             * @var int|null $order
             * @example 1
             */
            'order' => ['sometimes', 'integer', 'min:0'],

            /**
             * The status of the client section.
             *
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', new Enum(SectionStatusEnum::class)],
        ];
    }
}
