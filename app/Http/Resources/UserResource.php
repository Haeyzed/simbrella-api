<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * Represents a user resource.
 *
 * @package App\Http\Resources
 *
 * @property User $resource
 */
class UserResource extends JsonResource
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
             * The unique identifier for the user.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The first name of the user.
             *
             * @var string $first_name
             * @example "John"
             */
            'first_name' => $this->first_name,

            /**
             * The last name of the user.
             *
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => $this->last_name,

            /**
             * The full name of the user.
             *
             * @var string $full_name
             * @example "John Doe"
             */
            'full_name' => $this->full_name,

            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => $this->email,

            /**
             * The phone number of the user.
             *
             * @var string|null $phone
             * @example "+1 (555) 123-4567"
             */
            'phone' => $this->phone,

            /**
             * The biography of the user.
             *
             * @var string|null $bio
             * @example "John is a software developer with 10 years of experience."
             */
            'bio' => $this->bio,

            /**
             * The country of the user.
             *
             * @var string|null $country
             * @example "United States"
             */
            'country' => $this->country,

            /**
             * The state/province of the user.
             *
             * @var string|null $state
             * @example "California"
             */
            'state' => $this->state,

            /**
             * The postal code of the user.
             *
             * @var string|null $postal_code
             * @example "90210"
             */
            'postal_code' => $this->postal_code,

            /**
             * Indicates if the user's email is verified.
             *
             * @var bool $email_verified
             * @example true
             */
            'email_verified' => !is_null($this->email_verified_at),

            /**
             * The date when the user's email was verified.
             *
             * @var string|null $email_verified_at
             * @example "2023-06-15T10:00:00Z"
             */
            'email_verified_at' => $this->email_verified_at,

            /**
             * The profile image path of the user.
             *
             * @var string|null $profile_image
             * @example "users/profile-123456.jpg"
             */
            'profile_image' => $this->profile_image,

            /**
             * The profile image URL of the user.
             *
             * @var string|null $profile_image_url
             * @example "https://example.com/storage/users/profile-123456.jpg"
             */
            'profile_image_url' => $this->profile_image_url,

            /**
             * Indicates the user's active status.
             *
             * @var string $status
             * @example "active"
             */
            'status' => $this->status,

            /**
             * The human-readable label for the user's status.
             *
             * @var string $status_label
             * @example "Active"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The color associated with the user's status for UI display.
             *
             * @var string $status_color
             * @example "green"
             */
            'status_color' => $this->status ? $this->status->color() : null,

            /**
             * The roles assigned to the user.
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
             * The permissions assigned to the user.
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
             * The timestamp when the user was created.
             *
             * @var string $created_at
             * @example "2023-06-15T10:00:00Z"
             */
            'created_at' => $this->created_at,

            /**
             * The formatted date when the user was created.
             *
             * @var string $formatted_created_at
             * @example "June 15, 2023"
             */
            'formatted_created_at' => $this->created_at ? $this->created_at->format('F j, Y') : null,

            /**
             * The timestamp when the user was last updated.
             *
             * @var string $updated_at
             * @example "2023-06-15T11:00:00Z"
             */
            'updated_at' => $this->updated_at,

            /**
             * The timestamp when the user was deleted (if applicable).
             *
             * @var string|null $deleted_at
             * @example "2023-06-15T11:00:00Z"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
