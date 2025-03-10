<?php

namespace App\Models;

use App\Enums\SectionStatusEnum;
use App\Services\Storage\StorageService;
use Database\Factories\ClientSectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientSection extends Model
{
    /** @use HasFactory<ClientSectionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'logo_path',
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
     * Get the user that owns the client section.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the case study associated with the client section.
     *
     * @return HasOne
     */
    public function caseStudy(): HasOne
    {
        return $this->hasOne(CaseStudySection::class);
    }

    /**
     * Get the URL of the client's logo.
     *
     * @return string
     */
    public function getLogoUrlAttribute(): string
    {
        return app(StorageService::class)->url($this->logo_path);
    }
}
