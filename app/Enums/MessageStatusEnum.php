<?php

namespace App\Enums;

enum MessageStatusEnum: string
{
    case UNREAD = 'unread';
    case READ = 'read';
    case RESPONDED = 'responded';
    case ARCHIVED = 'archived';

    /**
     * Get all enum values.
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
            self::UNREAD => 'Unread',
            self::READ => 'Read',
            self::RESPONDED => 'Responded',
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
            self::UNREAD => 'red',
            self::READ => 'blue',
            self::RESPONDED => 'green',
            self::ARCHIVED => 'gray',
        };
    }
}
