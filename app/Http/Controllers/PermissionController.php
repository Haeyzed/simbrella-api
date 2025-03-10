<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListPermissionRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\ACLService;
use App\Services\PermissionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

/**
 * Permission Controller
 *
 * Handles CRUD operations for Permission management.
 * Includes methods to list, create, show, update, and delete Permission records.
 *
 * @package App\Http\Controllers\Api
 */
class PermissionController extends Controller
{
    /**
     * @var PermissionService
     */
    protected PermissionService $permissionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * PermissionController constructor.
     *
     * @param PermissionService $permissionService
     * @param ACLService $ACLService
     */
    public function __construct(PermissionService $permissionService, ACLService $ACLService)
    {
        $this->permissionService = $permissionService;
        $this->ACLService = $ACLService;
    }

    /**
     * Fetch a paginated list of permissions based on search and filter parameters.
     *
     * @param ListPermissionRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: PermissionResource[],
     *      meta: array{
     *          current_page: int,
     *          last_page: int,
     *          per_page: int,
     *          total: int
     *      }
     *  }
     */
    public function index(ListPermissionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.permission_view.name'));
            $permissions = $this->permissionService->list($request);
            return response()->paginatedSuccess(PermissionResource::collection($permissions), 'Permissions retrieved successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create and store a new permission.
     *
     * @param PermissionRequest $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PermissionResource
     *   }
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.permission_create.name'));
            $permission = $this->permissionService->create($request->validated());
            return response()->success(new PermissionResource($permission), 'Permission created successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Retrieve and display a specific permission.
     *
     * @param Permission $permission
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PermissionResource
     *   }
     */
    public function show(Permission $permission): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.permission_view.name'));
            $permission->load('roles');
            return response()->success(new PermissionResource($permission), 'Permission retrieved successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update an existing permission.
     *
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PermissionResource
     *   }
     */
    public function update(PermissionRequest $request, Permission $permission): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.permission_update.name'));
            $updatedPermission = $this->permissionService->update($permission, $request->validated());
            return response()->success(new PermissionResource($updatedPermission), 'Permission updated successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete a permission.
     *
     * @param Permission $permission
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.permission_delete.name'));
            $this->permissionService->delete($permission);
            return response()->success(null, 'Permission deleted successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get all permissions for dropdown lists.
     *
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PermissionResource[]
     *   }
     */
    public function getAllPermissions(): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.permission_view.name'));
            $permissions = $this->permissionService->getAllPermissions();
            return response()->success(PermissionResource::collection($permissions), 'All permissions retrieved successfully');
        } catch (Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
