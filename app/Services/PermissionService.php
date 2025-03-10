<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class PermissionService
{
    /**
     * List permissions based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of permissions.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return Permission::query()
            ->with(['roles'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['name', 'display_name', 'description'], $request->search);
            })
            ->when(
                $request->filled('order_by') && $request->filled('order_direction'),
                function ($query) use ($request) {
                    $query->orderBy($request->order_by, $request->order_direction);
                },
                function ($query) {
                    $query->orderBy('name', 'asc');
                }
            )
            ->paginate($request->integer('per_page', config('app.pagination.per_page')));
    }

    /**
     * Create a new permission.
     *
     * @param array $data The validated data for creating a new permission.
     * @return Permission The newly created permission.
     * @throws Exception
     */
    public function create(array $data): Permission
    {
        return DB::transaction(function () use ($data) {
            $this->validatePermissionName($data['name']);

            $permission = Permission::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'] ?? null,
                'description' => $data['description'] ?? null,
                'is_system' => false,
            ]);

            if (isset($data['roles']) && !empty($data['roles'])) {
                $permission->assignRole($data['roles']);
            }

            return $permission->load(['roles']);
        });
    }

    /**
     * Validate the permission name.
     *
     * @param string $name The permission name to validate.
     * @throws Exception If the permission name is reserved for system use.
     */
    private function validatePermissionName(string $name): void
    {
        $reservedNames = config('acl.reserved_permissions', []);
        if (in_array($name, $reservedNames)) {
            throw new Exception(
                'This permission name is reserved for system use.',
                Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * Update an existing permission.
     *
     * @param Permission $permission The permission to update.
     * @param array $data The validated data for updating the permission.
     * @return Permission The updated permission.
     * @throws Exception
     */
    public function update(Permission $permission, array $data): Permission
    {
        return DB::transaction(function () use ($permission, $data) {
            // Check if name is being changed and validate it
            if (isset($data['name']) && $permission->name !== $data['name']) {
                $this->validatePermissionName($data['name']);
            }

            // Prevent modifying system permissions
            $this->validateSystemPermission($permission);

            $permission->update([
                'name' => $data['name'] ?? $permission->name,
                'display_name' => $data['display_name'] ?? $permission->display_name,
                'description' => $data['description'] ?? $permission->description,
            ]);

            if (isset($data['roles'])) {
                $permission->syncRoles($data['roles']);
            }

            return $permission->load(['roles']);
        });
    }

    /**
     * Validate that a permission is not a protected system permission.
     *
     * @param Permission $permission The permission to validate.
     * @throws Exception If the permission is a system permission.
     */
    private function validateSystemPermission(Permission $permission): void
    {
        if ($permission->is_system) {
            throw new Exception(
                'System permissions cannot be modified.',
                Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * Delete a permission.
     *
     * @param Permission $permission The permission to delete.
     * @return bool|null The result of the delete operation.
     * @throws Exception
     */
    public function delete(Permission $permission): ?bool
    {
        return DB::transaction(function () use ($permission) {
            // Prevent deleting system permissions
            $this->validateSystemPermission($permission);

            // Check if the permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                throw new Exception(
                    'Cannot delete permission that is assigned to roles.',
                    Response::HTTP_CONFLICT
                );
            }

            return $permission->delete();
        });
    }

    /**
     * Get all permissions (for dropdowns, etc.)
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return Permission::orderBy('name')->get();
    }
}
