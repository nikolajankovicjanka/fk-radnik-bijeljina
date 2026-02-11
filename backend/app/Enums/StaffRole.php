<?php

namespace App\Enums;

enum StaffRole: string
{
    case HEAD_COACH = 'head_coach';
    case ASSISTANT_COACH = 'assistant_coach';
    case GK_COACH = 'gk_coach';
    case FITNESS_COACH = 'fitness_coach';
    case PHYSIO = 'physio';
    case DOCTOR = 'doctor';
    case ANALYST = 'analyst';
    case TEAM_MANAGER = 'team_manager';

    public function label() : string
    {
        return match ($this) {
            self::HEAD_COACH => 'Šef stručnog štaba',
            self::ASSISTANT_COACH => 'Pomoćni trener',
            self::GK_COACH => 'Trener golmana',
            self::FITNESS_COACH => 'Kondicioni trener',
            self::PHYSIO => 'Fizioterapeut',
            self::DOCTOR => 'Doktor',
            self::ANALYST => 'Analitičar',
            self::TEAM_MANAGER => 'Team manager',
        };
    }

    public static function options() : array
    {
        return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->all();
    }
}
