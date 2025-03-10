<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\GivePermissionRequest;
use App\Http\Requests\ListUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ACLService;
use App\Services\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * User Controller
 *
 * Handles CRUD operations and role/permission management for Users.
 * Includes methods to list, create, update, delete, restore, and manage roles/permissions for User records.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     * @param ACLService $ACLService
     */
    public function __construct(UserService $userService, ACLService $ACLService)
    {
        $this->userService = $userService;
        $this->ACLService = $ACLService;
    }

    /**
     * Fetch a paginated list of users based on search and filter parameters.
     *
     * @param ListUserRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: UserResource[],
     *      meta: array{
     *          current_page: int,
     *          last_page: int,
     *          per_page: int,
     *          total: int
     *      }
     *  }
     */
    public function index(ListUserRequest $request): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.user_view.name'));
        $users = $this->userService->list($request);
        return response()->paginatedSuccess(UserResource::collection($users), 'Users retrieved successfully');
    }

    /**
     * Create and store a new user.
     *
     * @param UserRequest $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.user_create.name'));
            $user = $this->userService->create($request->validated());
            return response()->success(new UserResource($user), 'User created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve and display a specific user.
     *
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function show(User $user): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.user_view.name'));
        return response()->success(new UserResource($user->load(['roles', 'permissions'])), 'User retrieved successfully');
    }

    /**
     * Update an existing user.
     *
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.user_update.name'));
            $updatedUser = $this->userService->update($user, $request->validated());
            return response()->success(new UserResource($updatedUser), 'User updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.user_delete.name'));

            $this->userService->delete($user);
            return response()->success(null, 'User deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a user.
     *
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function forceDelete(User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.user_force_delete.name'));

            $this->userService->forceDelete($user);
            return response()->success(null, 'User permanently deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to permanently delete user: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function restore(User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.user_restore.name'));
            $restoredUser = $this->userService->restore($user);
            return response()->success(new UserResource($restoredUser), 'User restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('User not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore user: ' . $e->getMessage());
        }
    }

    /**
     * Assign roles to a user.
     *
     * @param AssignRoleRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function assignRole(AssignRoleRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.assign_role.name'));
            $updatedUser = $this->userService->assignRoleToUser($user, $request->roles);
            return response()->success(new UserResource($updatedUser), 'Roles assigned successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to assign roles: ' . $e->getMessage());
        }
    }

    /**
     * Remove roles from a user.
     *
     * @param AssignRoleRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function removeRole(AssignRoleRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.remove_role.name'));
            $updatedUser = $this->userService->removeRoleFromUser($user, $request->roles);
            return response()->success(new UserResource($updatedUser), 'Roles removed successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to remove roles: ' . $e->getMessage());
        }
    }

    /**
     * Give permissions to a user.
     *
     * @param GivePermissionRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function givePermission(GivePermissionRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.give_permission.name'));
            $updatedUser = $this->userService->givePermissionToUser($user, $request->permissions);
            return response()->success(new UserResource($updatedUser), 'Permissions given successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to give permissions: ' . $e->getMessage());
        }
    }

    /**
     * Revoke permissions from a user.
     *
     * @param GivePermissionRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function revokePermission(GivePermissionRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.revoke_permission.name'));
            $updatedUser = $this->userService->revokePermissionFromUser($user, $request->permissions);
            return response()->success(new UserResource($updatedUser), 'Permissions revoked successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to revoke permissions: ' . $e->getMessage());
        }
    }

    /**
     * Change user password.
     *
     * @param ChangePasswordRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function changePassword(ChangePasswordRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.change_password.name'));
            $this->userService->changePassword($user, $request->password);
            return response()->success(null, 'Password changed successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Change user status.
     *
     * @param ChangeStatusRequest $request
     * @param User $user
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: UserResource
     *   }
     */
    public function changeStatus(ChangeStatusRequest $request, User $user): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.change_status.name'));
            $updatedUser = $this->userService->changeStatus($user, $request->status);
            return response()->success(new UserResource($updatedUser), 'Status changed successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to change status: ' . $e->getMessage());
        }
    }
}
