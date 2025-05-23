<?php

namespace App\Enums;

enum CareerStatusEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    /**
     * Get all values as an array.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the label for the enum value.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::ARCHIVED => 'Archived',
        };
    }

    /**
     * Get the color for the enum value.
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => '#ffc107', // Yellow
            self::PUBLISHED => '#28a745', // Green
            self::OPEN => '#ffc107', // Red
            self::CLOSED => '#dc3545', // Red
            self::ARCHIVED => '#6c757d', // Gray
        };
    }
}
