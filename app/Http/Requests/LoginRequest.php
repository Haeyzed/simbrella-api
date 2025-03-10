<?php

namespace App\Http\Requests;

class LoginRequest extends BaseRequest
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
             * The email address of the user.
             *
             * @var string $email
             * @example "superadmin@example.com"
             */
            'email' => ['required', 'string', 'email'],

            /**
             * The password for the user.
             *
             * @var string $password
             * @example "password"
             */
            'password' => ['required', 'string'],

            /**
             * Whether to remember the user.
             *
             * @var bool $remember
             * @example true
             */
            'remember' => ['sometimes', 'boolean'],
        ];
    }
}
