<?php

namespace App\Enums;

enum NewsCategory: string
{
    case FIRST_TEAM = 'first_team';
    case YOUTH = 'youth';
    case WOMEN = 'women';
    case CLUB = 'club';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_TEAM => 'Prvi tim',
            self::YOUTH => 'Omladinske selekcije',
            self::WOMEN => 'Ženski tim',
            self::CLUB => 'Klub',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
