<?php

namespace App\Models;

use App\Services\Storage\StorageService;
use Database\Factories\HeroImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroImage extends Model
{
    /** @use HasFactory<HeroImageFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hero_section_id',
        'image_path',
        'order',
    ];

    /**
     * Get the hero section that owns the image.
     *
     * @return BelongsTo
     */
    public function heroSection(): BelongsTo
    {
        return $this->belongsTo(HeroSection::class);
    }

    /**
     * Get the image URL.
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
}
