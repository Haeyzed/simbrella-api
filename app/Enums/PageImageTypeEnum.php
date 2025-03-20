<?php

namespace App\Enums;

enum PageImageTypeEnum: string
{
    case SERVICE_PAGE = 'service_page';
    case ABOUT_PAGE = 'about_page';
    case CAREER_PAGE = 'career_page';
    case CONTACT_PAGE = 'contact_page';
    case LOGO = 'logo';
    case NIGERIAN_FLAG = 'nigerian_flag';

    /**
     * Get the label for the enum value.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::SERVICE_PAGE => 'Service Page',
            self::ABOUT_PAGE => 'About Page',
            self::CAREER_PAGE => 'Career Page',
            self::CONTACT_PAGE => 'Contact Page',
            self::LOGO => 'Logo',
            self::NIGERIAN_FLAG => 'Nigerian Flag',
        };
    }

    /**
     * Get all enum values as an array.
     *
     * @return array<string, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum labels as an array.
     *
     * @return array<string, string>
     */
    public static function getLabels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }
}