<?php

namespace App\Http\Requests;

class AssignRoleRequest extends BaseRequest
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
             * The roles to assign to the user.
             *
             * @var array $roles
             * @example ["admin", "editor"]
             */
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'exists:roles,name'],
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

        return $attributes;
    }
}
