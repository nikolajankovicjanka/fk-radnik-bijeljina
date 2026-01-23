<?php

namespace App\Enums;

enum PlayerPosition: string
{
    case GK = 'GK';

    case CB = 'CB';
    case LB = 'LB';
    case RB = 'RB';

    case DM = 'DM';
    case CM = 'CM';
    case AM = 'AM';
    case LM = 'LM';
    case RM = 'RM';

    case FC = 'FC';

    public function label() : string
    {
        return match ($this) {
            self::GK => 'Golman (GK)',

            self::CB => 'Štoper (CB)',
            self::LB => 'Lijevi bek (LB)',
            self::RB => 'Desni bek (RB)',

            self::DM => 'Def. vezni (DM)',
            self::CM => 'Centralni vezni (CM)',
            self::AM => 'Ofanzivni vezni (AM)',
            self::LM => 'Lijevo krilo/vezni (LM)',
            self::RM => 'Desno krilo/vezni (RM)',

            self::FC => 'Napadač (FC)',
        };
    }

    public static function options() : array
    {
        return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->all();
    }
}
