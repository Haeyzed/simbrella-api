<?php

namespace App\Models;

use App\Enums\CareerStatusEnum;
use App\Enums\EmploymentTypeEnum;
use App\Services\Storage\StorageService;
use App\Traits\HasDateFilter;
use Database\Factories\CareerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    /** @use HasFactory<CareerFactory> */
    use HasFactory, SoftDeletes, HasDateFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'location',
        'format',
        'department',
        'employment_type',
        'salary_min',
        'salary_max',
        'currency',
        'application_email',
        'requirements',
        'benefits',
        'banner_image',
        'status',
        'published_at',
        'expires_at',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => CareerStatusEnum::class,
        'employment_type' => EmploymentTypeEnum::class,
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the career posting.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the banner image URL.
     *
     * @return string|null
     */
    public function getBannerImageUrlAttribute(): ?string
    {
        if (!$this->banner_image) {
            return null;
        }

        return app(StorageService::class)->url($this->banner_image);
    }

    /**
     * Get the formatted salary range.
     *
     * @return string|null
     */
    public function getSalaryRangeAttribute(): ?string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return null;
        }

        if ($this->salary_min && !$this->salary_max) {
            return "From {$this->currency} {$this->salary_min}";
        }

        if (!$this->salary_min && $this->salary_max) {
            return "Up to {$this->currency} {$this->salary_max}";
        }

        return "{$this->currency} {$this->salary_min} - {$this->salary_max}";
    }

    /**
     * Scope a query to only include active job postings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', CareerStatusEnum::PUBLISHED)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
