<?php

namespace App\Http\Controllers;

use App\Http\Requests\GivePermissionRequest;
use App\Http\Requests\ListRoleRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\ACLService;
use App\Services\RoleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

/**
 * Role Controller
 *
 * Handles CRUD operations and permission management for Roles.
 * Includes methods to list, create, update, delete, and manage permissions for Role records.
 *
 * @package App\Http\Controllers
 */
class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected RoleService $roleService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     * @param ACLService $ACLService
     */
    public function __construct(RoleService $roleService, ACLService $ACLService)
    {
        $this->roleService = $roleService;
        $this->ACLService = $ACLService;
    }

    /**
     * Fetch a paginated list of roles based on search and filter parameters.
     *
     * @param ListRoleRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: RoleResource[],
     *      meta: array{
     *          current_page: int,
     *          last_page: int,
     *          per_page: int,
     *          total: int
     *      }
     *  }
     */
    public function index(ListRoleRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_view.name'));
            $roles = $this->roleService->list($request);
            return response()->paginatedSuccess(RoleResource::collection($roles), 'Roles retrieved successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create and store a new role.
     *
     * @param RoleRequest $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: RoleResource
     *   }
     */
    public function store(RoleRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_create.name'));
            $role = $this->roleService->create($request->validated());
            return response()->success(new RoleResource($role), 'Role created successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Retrieve and display a specific role.
     *
     * @param Role $role
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: RoleResource
     *   }
     */
    public function show(Role $role): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_view.name'));
            $role->load('permissions');
            return response()->success(new RoleResource($role), 'Role retrieved successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update an existing role.
     *
     * @param RoleRequest $request
     * @param Role $role
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: RoleResource
     *   }
     */
    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_update.name'));
            $updatedRole = $this->roleService->update($role, $request->validated());
            return response()->success(new RoleResource($updatedRole), 'Role updated successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete a role.
     *
     * @param Role $role
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_delete.name'));
            $this->roleService->delete($role);
            return response()->success(null, 'Role deleted successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Assign permissions to a role.
     *
     * @param GivePermissionRequest $request
     * @param Role $role
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: RoleResource
     *   }
     */
    public function assignPermissions(GivePermissionRequest $request, Role $role): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_update.name'));
            $updatedRole = $this->roleService->assignPermissionsToRole($role, $request->permissions);
            return response()->success(new RoleResource($updatedRole), 'Permissions assigned successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove permissions from a role.
     *
     * @param GivePermissionRequest $request
     * @param Role $role
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: RoleResource
     *   }
     */
    public function removePermissions(GivePermissionRequest $request, Role $role): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_update.name'));
            $updatedRole = $this->roleService->removePermissionsFromRole($role, $request->permissions);
            return response()->success(new RoleResource($updatedRole), 'Permissions removed successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get all roles for dropdown lists.
     *
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: RoleResource[]
     *   }
     */
    public function getAllRoles(): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.role_view.name'));
            $roles = $this->roleService->getAllRoles();
            return response()->success(RoleResource::collection($roles), 'All roles retrieved successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
