<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends BaseRequest
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
        $user = $this->route('user');
        $userId = $user ? $user->id : null;
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
            'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($userId)],

            /**
             * The phone number of the user.
             *
             * @var string|null $phone
             * @example "+1 (555) 123-4567"
             */
            'phone' => ['nullable', 'string', 'max:20'],

            /**
             * The biography of the user.
             *
             * @var string|null $bio
             * @example "John is a software developer with 10 years of experience."
             */
            'bio' => ['nullable', 'string', 'max:1000'],

            /**
             * The country of the user.
             *
             * @var string|null $country
             * @example "United States"
             */
            'country' => ['nullable', 'string', 'max:100'],

            /**
             * The state/province of the user.
             *
             * @var string|null $state
             * @example "California"
             */
            'state' => ['nullable', 'string', 'max:100'],

            /**
             * The postal code of the user.
             *
             * @var string|null $postal_code
             * @example "90210"
             */
            'postal_code' => ['nullable', 'string', 'max:20'],

            /**
             * The status of the user account.
             *
             * @var StatusEnum $status
             * @example \App\Enums\StatusEnum::active
             */
            'status' => ['nullable', 'string', Rule::in(StatusEnum::values())],

            /**
             * The password for the user's account.
             *
             * This field is required for new users and optional for updates. It must be
             * at least 8 characters long.
             *
             * @var string|null $password
             * @example "secureP@ssw0rd"
             */
            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                Password::min(8)
                    ->mixedCase()    // Must contain both uppercase and lowercase letters
                    ->letters()       // Must contain at least one letter
                    ->numbers()       // Must contain at least one number
                    ->symbols()       // Must contain at least one symbol
                    ->uncompromised(), // Check that the password is not in data breaches
            ],

            /**
             * The profile image for the user.
             *
             * @var file|null $profile_image
             */
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            /**
             * The roles assigned to the user.
             *
             * This field is optional and must be an array of existing role names.
             *
             * @var array $roles
             * @example ["admin", "editor"]
             */
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],

            /**
             * The permissions assigned to the user.
             *
             * This field is optional and must be an array of existing permission names.
             *
             * @var array $permissions
             * @example ["Create users", "Update users", "Delete users"]
             */
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [];

        if ($this->has('roles')) {
            foreach ($this->input('roles') as $index => $role) {
                $attributes["roles.{$index}"] = "role '{$role}'";
            }
        }

        if ($this->has('permissions')) {
            foreach ($this->input('permissions') as $index => $permission) {
                $attributes["permissions.{$index}"] = "permission '{$permission}'";
            }
        }

        return $attributes;
    }
}
