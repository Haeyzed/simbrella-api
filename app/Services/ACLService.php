<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ACLService
{
    /**
     * Check if the user has the specified permission.
     *
     * @param string $permission
     * @return bool
     * @throws AccessDeniedHttpException
     */
    public function checkUserPermission(string $permission): bool
    {
        $user = Auth::user();
        $displayName = config('acl.permissions.' . $permission . '.display_name', $permission);

        if (!$user) {
            throw new AccessDeniedHttpException('Unauthenticated.', null, Response::HTTP_UNAUTHORIZED);
        }

        if ($this->checkSuperUser()) {
            return true;
        }

        if (!$user->hasPermissionTo($permission)) {
            throw new AccessDeniedHttpException(
                'You do not have permission to ' . $displayName . '.',
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        return true;
    }

    /**
     * Check if the user is a super admin.
     *
     * @return bool
     */
    private function checkSuperUser(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole(config('acl.roles.sadmin.name'));
    }

    /**
     * Check if the user has any of the specified permissions.
     *
     * @param array $permissions
     * @return bool
     * @throws AccessDeniedHttpException
     */
    public function checkUserAnyPermission(array $permissions): bool
    {
        $user = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException('Unauthenticated.', null, Response::HTTP_UNAUTHORIZED);
        }

        if ($this->checkSuperUser()) {
            return true;
        }

        if (!$user->hasAnyPermission($permissions)) {
            throw new AccessDeniedHttpException(
                'You do not have any of the required permissions.',
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        return true;
    }

    /**
     * Check if the user has all the specified permissions.
     *
     * @param array $permissions
     * @return bool
     * @throws AccessDeniedHttpException
     */
    public function checkUserAllPermissions(array $permissions): bool
    {
        $user = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException('Unauthenticated.', null, Response::HTTP_UNAUTHORIZED);
        }

        if ($this->checkSuperUser()) {
            return true;
        }

        if (!$user->hasAllPermissions($permissions)) {
            throw new AccessDeniedHttpException(
                'You do not have all the required permissions.',
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        return true;
    }

    /**
     * Check if the user has the specified role.
     *
     * @param string|array $roles
     * @return bool
     * @throws AccessDeniedHttpException
     */
    public function checkUserRole($roles): bool
    {
        $user = Auth::user();

        if (!$user) {
            throw new AccessDeniedHttpException('Unauthenticated.', null, Response::HTTP_UNAUTHORIZED);
        }

        if ($this->checkSuperUser()) {
            return true;
        }

        if (is_array($roles)) {
            if (!$user->hasAnyRole($roles)) {
                throw new AccessDeniedHttpException(
                    'You do not have any of the required roles.',
                    null,
                    Response::HTTP_FORBIDDEN
                );
            }
        } else {
            if (!$user->hasRole($roles)) {
                throw new AccessDeniedHttpException(
                    'You do not have the required role.',
                    null,
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        return true;
    }
}
