<?php

namespace App\Http\Requests;

class PermissionRequest extends BaseRequest
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
             * The name of the permission.
             *
             * @var string $name
             * @example "edit_users"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The display name of the permission.
             *
             * @var string|null $display_name
             * @example "Edit Users"
             */
            'display_name' => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * The description of the permission.
             *
             * @var string|null $description
             * @example "Allows a user to edit other users."
             */
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],

            /**
             * The roles associated with the permission.
             *
             * @var array $roles
             * @example ["admin", "editor"]
             */
            'roles' => ['sometimes', 'array'],

            /**
             * Each role name in the roles array.
             *
             * @var string $roles.*
             * @example "admin"
             */
            'roles.*' => ['string', 'exists:roles,name'],
        ];

        // Add unique rule for name when creating a new permission or updating with a different name
        if ($this->isMethod('POST') ||
            ($this->isMethod('PUT') && $this->route('permission')->name !== $this->input('name'))) {
            $rules['name'][] = 'unique:permissions,name';
        }

        return $rules;
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

        return $attributes;
    }
}
