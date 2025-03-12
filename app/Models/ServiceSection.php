<?php

namespace App\Models;

use App\Enums\SectionStatusEnum;
use App\Services\Storage\StorageService;
use App\Traits\HasDateFilter;
use Database\Factories\ServiceSectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceSection extends Model
{
    /** @use HasFactory<ServiceSectionFactory> */
    use HasFactory, SoftDeletes, HasDateFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'title_short',
        'summary',
        'summary_short',
        'icon_path',
        'image_path',
        'order',
        'status',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => SectionStatusEnum::class,
    ];

    /**
     * Get the image URL attribute.
     *
     * @return string|null
     */
    public function getIconUrlAttribute(): ?string
    {
        if (!$this->icon_path) {
            return null;
        }

        return app(StorageService::class)->url($this->icon_path);
    }

    /**
     * Get the image URL attribute.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return app(StorageService::class)->url($this->image_path);
    }

    /**
     * Get the user that owns the service section.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
