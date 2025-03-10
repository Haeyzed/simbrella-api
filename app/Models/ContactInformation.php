<?php

namespace App\Models;

use App\Traits\HasDateFilter;
use Database\Factories\ContactInformationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactInformation extends Model
{
    /** @use HasFactory<ContactInformationFactory> */
    use HasFactory, SoftDeletes, HasDateFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'phone',
        'email',
        'facebook_link',
        'instagram_link',
        'linkedin_link',
        'twitter_link',
        'user_id',
    ];

    /**
     * Get the user that owns the contact information.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
