<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Traits\HasDateFilter;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject, Auditable, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Notifiable, HasDateFilter, SoftDeletes, HasRoles, HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'bio',
        'country',
        'state',
        'postal_code',
        'password',
        'status',
        'email_verified_at',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => StatusEnum::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the profile image URL attribute.
     *
     * @return string|null
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (!$this->profile_image) {
            return null;
        }

        return asset('storage/' . $this->profile_image);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }

        return !!$permission->intersect($this->permissions)->count();
    }

    /**
     * Remove a role from the user.
     *
     * @param string $role
     * @return int
     */
    public function removeRole(string $role): int
    {
        return $this->roles()->detach($role);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Remove a permission from the user.
     *
     * @param $permission
     * @return int
     */
    public function removePermission($permission): int
    {
        return $this->permissions()->detach($permission);
    }

    /**
     * The permissions that belong to the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Get the OTPs for the user.
     *
     * @return HasMany
     */
    public function otps(): HasMany
    {
        return $this->hasMany(OTP::class);
    }

    /**
     * Get the blog posts for the user.
     *
     * @return HasMany
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get the careers for the user.
     *
     * @return HasMany
     */
    public function careers(): HasMany
    {
        return $this->hasMany(Career::class);
    }

    /**
     * Get the hero sections for the user.
     *
     * @return HasMany
     */
    public function heroSections(): HasMany
    {
        return $this->hasMany(HeroSection::class);
    }

    /**
     * Get the service sections for the user.
     *
     * @return HasMany
     */
    public function serviceSections(): HasMany
    {
        return $this->hasMany(ServiceSection::class);
    }

    /**
     * Get the about sections for the user.
     *
     * @return HasMany
     */
    public function aboutSections(): HasMany
    {
        return $this->hasMany(AboutSection::class);
    }

    /**
     * Get the product sections for the user.
     *
     * @return HasMany
     */
    public function productSections(): HasMany
    {
        return $this->hasMany(ProductSection::class);
    }

    /**
     * Get the client sections for the user.
     *
     * @return HasMany
     */
    public function clientSections(): HasMany
    {
        return $this->hasMany(ClientSection::class);
    }

    /**
     * Get the case study sections for the user.
     *
     * @return HasMany
     */
    public function caseStudySections(): HasMany
    {
        return $this->hasMany(CaseStudySection::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
