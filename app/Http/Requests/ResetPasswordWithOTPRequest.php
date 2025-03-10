<?php

namespace App\Http\Requests;

class ResetPasswordWithOTPRequest extends BaseRequest
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
             * @example "user@example.com"
             */
            'email' => ['required', 'string', 'email', 'exists:users,email'],

            /**
             * The OTP code to verify.
             *
             * @var string $otp
             * @example "123456"
             */
            'otp' => ['required', 'string', 'size:6'],

            /**
             * The new password for the user.
             *
             * @var string $password
             * @example "newSecureP@ssw0rd"
             */
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
