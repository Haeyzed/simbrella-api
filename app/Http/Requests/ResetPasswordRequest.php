<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends BaseRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The email address of the user resetting their password.
             *
             * This field is required and must be a valid email address format.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => ['required', 'email'],

            /**
             * The new password for the user's account.
             *
             * This field is required, must be at least 8 characters long, and should be
             * confirmed by a matching password_confirmation field.
             *
             * @var string $password
             * @example "password"
             */
            'password' => ['required', 'string', 'confirmed',
                Password::min(8)
                    ->mixedCase()    // Must contain both uppercase and lowercase letters
                    ->letters()       // Must contain at least one letter
                    ->numbers()       // Must contain at least one number
                    ->symbols()       // Must contain at least one symbol
                ],

            /**
             * The token received in the password reset email.
             *
             * This field is required and must be a string. It's used to verify the
             * validity of the password reset request.
             *
             * @var string $token
             * @example "60a9c2ff3d5e37a5c366b6802c4f9412e40c2011db868378e1ddfd645d181825"
             */
            'token' => ['required', 'string'],
        ];
    }
}
