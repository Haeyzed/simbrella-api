<?php

namespace App\Enums;

enum EmploymentTypeEnum: string
{
    case FULLTIME = 'full-time';
    case PARTTIME = 'part-time';
    case CONTRACT = 'contract';

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
        return match($this) {
            self::FULLTIME => 'Full time',
            self::PARTTIME => 'Part time',
            self::CONTRACT => 'Contract',
        };
    }
}
