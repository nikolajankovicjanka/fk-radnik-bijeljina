<?php

namespace App\Enums;

enum TeamType: string
{
    case FIRST_TEAM = 'first_team';
    case YOUTH = 'youth';
    case WOMEN = 'women';

    public function label() : string
    {
        return match ($this) {
            self::FIRST_TEAM => 'Prvi tim',
            self::YOUTH => 'Omladinski pogon',
            self::WOMEN => 'Žene',
        };
    }

    public static function options() : array
    {
        return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->all();
    }
}
