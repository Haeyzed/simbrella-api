<?php

namespace App\Models;

use App\Enums\SectionStatusEnum;
use App\Services\Storage\StorageService;
use Database\Factories\CaseStudySectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudySection extends Model
{
    /** @use HasFactory<CaseStudySectionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_section_id',
        'banner_image',
        'company_name',
        'subtitle',
        'description',
        'challenge',
        'solution',
        'results',
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
     * Get the user that owns the case study section.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client section associated with the case study.
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSection::class, 'client_section_id');
    }

    /**
     * Get the URL of the banner image.
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
}
