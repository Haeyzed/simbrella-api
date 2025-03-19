<?php

namespace App\Services;

use App\Models\User;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * UserService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List users based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of users.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['first_name', 'last_name', 'email', 'phone'], $request->search);
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->input('role'), function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($request->input('country'), function ($query, $country) {
                $query->where('country', $country);
            })
            ->when($request->input('state'), function ($query, $state) {
                $query->where('state', $state);
            })
            ->when(
                $request->filled('order_by') && $request->filled('order_direction'),
                function ($query) use ($request) {
                    $query->orderBy($request->order_by, $request->order_direction);
                },
                function ($query) {
                    $query->latest();
                }
            )
            ->when($request->boolean('trashed_only'), function ($query) {
                $query->onlyTrashed();
            })
            ->filterByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            )
            ->with(['roles', 'permissions'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new user.
     *
     * @param array $data The validated data for creating a new user.
     * @return User The newly created user.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Generate a random password if not provided
            if (!isset($data['password'])) {
                $data['password'] = Str::password(10);
            }

            // Handle profile image
            if (isset($data['profile_image']) && $data['profile_image'] instanceof UploadedFile) {
                $data['profile_image'] = $this->uploadProfileImage($data['profile_image']);
            }

            // Create the user
            $user = User::create($this->prepareUserData($data));

            // Handle roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->assignRole($data['roles']);
            }

            // Handle permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->givePermissionTo($data['permissions']);
            }

            return $user->load(['roles', 'permissions']);
        });
    }

    /**
     * Upload a profile image.
     *
     * @param UploadedFile $image The image file to upload.
     * @return string The path to the uploaded image.
     */
    private function uploadProfileImage(UploadedFile $image): string
    {
        return $this->storageService->upload(
            $image,
            config('filestorage.paths.user_profiles', 'users'),
            ['resize' => [300, 300]]
        );
    }

    /**
     * Prepare user data for creation or update.
     *
     * @param array $data The input data.
     * @param User|null $user The user being updated, if any.
     * @return array The prepared user data.
     */
    private function prepareUserData(array $data, ?User $user = null): array
    {
        // Hash the password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif ($user === null) {
            // For new users without a password, generate one
            $data['password'] = Hash::make(Str::password(10));
        } else {
            // For existing users, remove password from data if not provided
            unset($data['password']);
        }

        // Remove roles and permissions from data as they're handled separately
        unset($data['roles'], $data['permissions']);

        return $data;
    }

    /**
     * Force delete a user.
     *
     * @param User $user The user to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(User $user): ?bool
    {
        return DB::transaction(function () use ($user) {
            // Delete profile image if exists
            if ($user->profile_image) {
                $this->storageService->delete($user->profile_image);
            }

            // Remove roles and permissions first
            $user->roles()->detach();
            $user->permissions()->detach();

            return $user->forceDelete();
        });
    }

    /**
     * Delete a user.
     *
     * @param User $user The user to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(User $user): ?bool
    {
        return DB::transaction(function () use ($user) {
            return $user->delete();
        });
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param User $user The user to restore.
     * @return User The restored user.
     */
    public function restore(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->restore();
            return $user->load(['roles', 'permissions']);
        });
    }

    /**
     * Assign roles to a user.
     *
     * @param User $user The user to assign roles to.
     * @param array $roles The roles to assign.
     * @return User The updated user.
     */
    public function assignRoleToUser(User $user, array $roles): User
    {
        $roleIds = Role::query()->whereIn('name', $roles)->pluck('id')->toArray();
        $permissionIds = DB::table('role_has_permissions')
            ->whereIn('role_id', $roleIds)
            ->pluck('permission_id')
            ->unique()
            ->toArray();

        $permissions = Permission::query()->whereIn('id', $permissionIds)->pluck('name')->toArray();

        DB::transaction(function () use ($user, $roles, $permissions, $permissionIds) {
            $user->syncRoles($roles);
            $user->givePermissionTo($permissionIds);
        });

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Remove roles from a user.
     *
     * @param User $user The user to remove roles from.
     * @param array $roles The roles to remove.
     * @return User The updated user.
     */
    public function removeRoleFromUser(User $user, array $roles): User
    {
        $roleIds = Role::query()->whereIn('name', $roles)->pluck('id')->toArray();
        $permissionIds = DB::table('role_has_permissions')
            ->whereIn('role_id', $roleIds)
            ->pluck('permission_id')
            ->unique()
            ->toArray();

        $permissions = Permission::query()->whereIn('id', $permissionIds)->pluck('name')->toArray();

        DB::transaction(function () use ($user, $roles, $permissions, $permissionIds) {
            $user->syncRoles($roles);
            $user->removePermission($permissionIds);
        });

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Give permissions to a user.
     *
     * @param User $user The user to give permissions to.
     * @param array $permissions The permissions to give.
     * @return User The updated user.
     */
    public function givePermissionToUser(User $user, array $permissions): User
    {
        // Get current direct permissions
        $currentPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        // Merge with new permissions
        $allPermissions = array_unique(array_merge($currentPermissions, $permissions));

        // Sync permissions
        $user->syncPermissions($allPermissions);

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Revoke permissions from a user.
     *
     * @param User $user The user to revoke permissions from.
     * @param array $permissions The permissions to revoke.
     * @return User The updated user.
     */
    public function revokePermissionFromUser(User $user, array $permissions): User
    {
        // Get current direct permissions
        $currentPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        // Remove permissions to revoke
        $remainingPermissions = array_diff($currentPermissions, $permissions);

        // Sync remaining permissions
        $user->syncPermissions($remainingPermissions);

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Change user password.
     *
     * @param User $user The user to change password for.
     * @param string $newPassword The new password.
     * @return User The updated user.
     */
    public function changePassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param User $user The user to update.
     * @param array $data The validated data for updating the user.
     * @return User The updated user.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Handle profile image
            if (isset($data['profile_image']) && $data['profile_image'] instanceof UploadedFile) {
                // Delete old profile image if exists
                if ($user->profile_image) {
                    $this->storageService->delete($user->profile_image);
                }

                $data['profile_image'] = $this->uploadProfileImage($data['profile_image']);
            }

            // Update user data
            $user->update($this->prepareUserData($data, $user));

            // Handle roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            // Handle permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            return $user->load(['roles', 'permissions']);
        });
    }

    /**
     * Change user status.
     *
     * @param User $user The user to change status for.
     * @param string $status The new status.
     * @return User The updated user.
     */
    public function changeStatus(User $user, string $status): User
    {
        $user->update([
            'status' => $status
        ]);

        return $user;
    }
}
