<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

/**
 * Class RoleResource
 *
 * Represents a role resource.
 *
 * @package App\Http\Resources
 *
 *
 * @property Role $resource
 */
class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the role.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the role.
             *
             * @var string $name
             * @example "admin"
             */
            'name' => $this->name,

            /**
             * The display name of the role.
             *
             * @var string|null $display_name
             * @example "Administrator"
             */
            'display_name' => $this->display_name,

            /**
             * The description of the role.
             *
             * @var string|null $description
             * @example "This role grants full access to the system."
             */
            'description' => $this->description,

            /**
             * Indicates if the role is a system-defined role.
             *
             * @var bool $is_system
             * @example true
             */
            'is_system' => $this->is_system ?? false,

            /**
             * The permissions assigned to the role.
             *
             * @var array|null $permissions
             * @example [{"id": 1, "name": "manage-users", "display_name": "Manage users"}, {"id": 2, "name": "edit-settings", "display_name": "Edit settings"}]
             */
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => $permission->display_name ?? ucfirst(str_replace('_', ' ', $permission->name)),
                    ];
                });
            }),

            /**
             * The timestamp when the role was created.
             *
             * @var string $created_at
             * @example "2023-06-15T10:00:00Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the role was last updated.
             *
             * @var string $updated_at
             * @example "2023-06-15T11:00:00Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
