<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseRequest
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
             * The first name of the user.
             *
             * @var string $first_name
             * @example "John"
             */
            'first_name' => ['required', 'string', 'max:255'],

            /**
             * The last name of the user.
             *
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => ['required', 'string', 'max:255'],

            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "user@example.com"
             */
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            /**
             * The password for the user.
             *
             * @var string $password
             * @example "secureP@ssw0rd"
             */
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            /**
             * The roles to assign to the user.
             *
             * @var array $roles
             * @example ["user"]
             */
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],

            /**
             * The permissions to assign to the user.
             *
             * @var array $permissions
             * @example ["view_dashboard"]
             */
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
