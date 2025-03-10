<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class PermissionResource
 *
 * Represents a permission resource.
 *
 * @package App\Http\Resources
 *
 *
 * @property Permission $resource
 */
class PermissionResource extends JsonResource
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
             * The unique identifier for the permission.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the permission.
             *
             * @var string $name
             * @example "manage-users"
             */
            'name' => $this->name,

            /**
             * The display name of the permission.
             *
             * @var string|null $display_name
             * @example "Manage Users"
             */
            'display_name' => $this->display_name,

            /**
             * The description of the permission.
             *
             * @var string|null $description
             * @example "Allows managing user accounts."
             */
            'description' => $this->description,

            /**
             * Indicates if the permission is a system-defined permission.
             *
             * @var bool $is_system
             * @example true
             */
            'is_system' => $this->is_system ?? false,

            /**
             * The roles associated with this permission.
             *
             * @var array|null $roles
             * @example [{"id": 1, "name": "admin", "display_name": "Administrator"}, {"id": 2, "name": "editor", "display_name": "Editor"}]
             */
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name ?? ucfirst($role->name),
                    ];
                });
            }),

            /**
             * The timestamp when the permission was created.
             *
             * @var string $created_at
             * @example "2023-06-15T10:00:00Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the permission was last updated.
             *
             * @var string $updated_at
             * @example "2023-06-15T11:00:00Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
