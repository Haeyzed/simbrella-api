<?php

namespace App\Http\Requests;

class UpdateProfileRequest extends BaseRequest
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
             * The name of the user.
             *
             * @var string $name
             * @example "John Doe"
             */
            'name' => ['sometimes', 'string', 'max:255'],

            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "user@example.com"
             */
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ];
    }
}
