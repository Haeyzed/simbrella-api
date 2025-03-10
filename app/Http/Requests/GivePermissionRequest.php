<?php

namespace App\Http\Requests;

class GivePermissionRequest extends BaseRequest
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
             * The permissions to give to the user.
             *
             * @var array $permissions
             * @example ["create_users", "update_users"]
             */
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', 'exists:permissions,name'],
        ];
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
