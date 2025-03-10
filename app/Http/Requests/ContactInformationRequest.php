<?php

namespace App\Http\Requests;

class ContactInformationRequest extends BaseRequest
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
             * The address of the company.
             *
             * @var string $address
             * @example "123 Business Street, City, Country"
             */
            'address' => ['required', 'string', 'max:1000'],

            /**
             * The phone number of the company.
             *
             * @var string $phone
             * @example "+1234567890"
             */
            'phone' => ['required', 'string', 'max:20'],

            /**
             * The email address of the company.
             *
             * @var string $email
             * @example "contact@company.com"
             */
            'email' => ['required', 'email', 'max:255'],

            /**
             * The Facebook link of the company.
             *
             * @var string|null $facebook_link
             * @example "https://facebook.com/company"
             */
            'facebook_link' => ['nullable', 'url', 'max:255'],

            /**
             * The Instagram link of the company.
             *
             * @var string|null $instagram_link
             * @example "https://instagram.com/company"
             */
            'instagram_link' => ['nullable', 'url', 'max:255'],

            /**
             * The LinkedIn link of the company.
             *
             * @var string|null $linkedin_link
             * @example "https://linkedin.com/company"
             */
            'linkedin_link' => ['nullable', 'url', 'max:255'],

            /**
             * The Twitter link of the company.
             *
             * @var string|null $twitter_link
             * @example "https://twitter.com/company"
             */
            'twitter_link' => ['nullable', 'url', 'max:255'],
        ];
    }
}
