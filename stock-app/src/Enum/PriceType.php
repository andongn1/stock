<?php

namespace App\Enum;

enum PriceType: string
{
    case PER_UNIT = 'per_unit';
    case PER_PACKAGING = 'per_packaging';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PER_UNIT => 'Par unite',
            self::PER_PACKAGING => 'Par conditionnement',
        };
    }

    public static function options(): array
    {
        return [
            self::PER_UNIT->value => self::PER_UNIT->label(),
            self::PER_PACKAGING->value => self::PER_PACKAGING->label(),
        ];
    }
}
