<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class RoleService
{
    /**
     * List roles based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of roles.
     */
    public function list(object $request): LengthAwarePaginator
    {
        $user = auth()->user();

        return Role::query()
            ->with(['permissions'])
            ->when(!$user->hasRole(config('acl.roles.sadmin.name')), function ($query) {
                $query->whereNotIn('name', [
                    config('acl.roles.sadmin.name')
                ]);
            })
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
     * Create a new role.
     *
     * @param array $data The validated data for creating a new role.
     * @return Role The newly created role.
     * @throws Exception
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            // Check if the role name is reserved
            if ($data['name'] === config('acl.roles.sadmin.name')) {
                throw new Exception('Cannot create super admin role.', Response::HTTP_FORBIDDEN);
            }

            $role = Role::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            if (isset($data['permissions']) && !empty($data['permissions'])) {
                $role->givePermissionTo($data['permissions']);
            }

            return $role->load(['permissions']);
        });
    }

    /**
     * Update an existing role.
     *
     * @param Role $role The role to update.
     * @param array $data The validated data for updating the role.
     * @return Role The updated role.
     * @throws Exception
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            // Prevent updating super admin role name
            if ($role->name === config('acl.roles.sadmin.name') && isset($data['name']) && $data['name'] !== $role->name) {
                throw new Exception('Cannot change super admin role name.', Response::HTTP_FORBIDDEN);
            }

            $role->update([
                'name' => $data['name'] ?? $role->name,
                'display_name' => $data['display_name'] ?? $role->display_name,
                'description' => $data['description'] ?? $role->description,
            ]);

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->load(['permissions']);
        });
    }

    /**
     * Delete a role.
     *
     * @param Role $role The role to delete.
     * @return bool|null The result of the delete operation.
     * @throws Exception
     */
    public function delete(Role $role): ?bool
    {
        return DB::transaction(function () use ($role) {
            // Prevent deleting super admin or other system roles
            if ($role->name === config('acl.roles.sadmin.name')) {
                throw new Exception('Cannot delete super admin role.', Response::HTTP_FORBIDDEN);
            }

            // Check if the role is assigned to any users
            if ($role->users()->count() > 0) {
                throw new Exception('Cannot delete role that is assigned to users.', Response::HTTP_CONFLICT);
            }

            return $role->delete();
        });
    }

    /**
     * Assign permissions to a role.
     *
     * @param Role $role The role to assign permissions to.
     * @param array $permissions The permissions to assign.
     * @return Role The updated role.
     * @throws Exception
     */
    public function assignPermissionsToRole(Role $role, array $permissions): Role
    {
        return DB::transaction(function () use ($role, $permissions) {
            // Prevent modifying super admin role permissions
            if ($role->name === config('acl.roles.sadmin.name')) {
                throw new Exception('Cannot modify super admin role permissions.', Response::HTTP_FORBIDDEN);
            }

            $role->givePermissionTo($permissions);
            return $role->load('permissions');
        });
    }

    /**
     * Remove permissions from a role.
     *
     * @param Role $role The role to remove permissions from.
     * @param array $permissions The permissions to remove.
     * @return Role The updated role.
     * @throws Exception
     */
    public function removePermissionsFromRole(Role $role, array $permissions): Role
    {
        return DB::transaction(function () use ($role, $permissions) {
            // Prevent modifying super admin role permissions
            if ($role->name === config('acl.roles.sadmin.name')) {
                throw new Exception('Cannot modify super admin role permissions.', Response::HTTP_FORBIDDEN);
            }

            $role->revokePermissionTo($permissions);
            return $role->load('permissions');
        });
    }

    /**
     * Get all roles (for dropdowns, etc.)
     *
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        $user = auth()->user();

        return Role::query()
            ->when(!$user->hasRole(config('acl.roles.sadmin.name')), function ($query) {
                $query->whereNotIn('name', [
                    config('acl.roles.sadmin.name')
                ]);
            })
            ->orderBy('name')
            ->get();
    }
}
