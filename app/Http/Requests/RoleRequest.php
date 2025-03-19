<?php

namespace App\Http\Requests;

class RoleRequest extends BaseRequest
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
        $rules = [
            /**
             * The name of the role.
             *
             * @var string $name
             * @example "admin"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The display name of the role.
             *
             * @var string|null $display_name
             * @example "Administrator"
             */
            'display_name' => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * The description of the role.
             *
             * @var string|null $description
             * @example "Has full access to all system features."
             */
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],

            /**
             * The permissions associated with the role.
             *
             * @var array $permissions
             * @example ["view_dashboard", "manage_users"]
             */
            'permissions' => ['sometimes', 'array'],

            /**
             * Each permission name in the permissions array.
             *
             * @var string $permissions .*
             * @example "manage_users"
             */
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];

        // Add unique rule for name when creating a new role or updating with a different name
        if ($this->isMethod('POST') ||
            ($this->isMethod('PUT') && $this->route('role')->name !== $this->input('name'))) {
            $rules['name'][] = 'unique:roles,name';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [];

        if ($this->has('permissions')) {
            foreach ($this->input('permissions') as $index => $permission) {
                $attributes["permissions.{$index}"] = "permission '{$permission}'";
            }
        }

        return $attributes;
    }
}
