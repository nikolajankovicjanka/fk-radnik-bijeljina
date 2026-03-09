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

    case PRESIDENT = 'president';
    case GENERAL_DIRECTOR = 'general_director';
    case SPORT_DIRECTOR = 'sport_director';
    case BOARD_SECRETARY = 'board_secretary';
    case CLUB_SECRETARY = 'club_secretary';
    case BOARD_MEMBER = 'board_member';
    case YOUTH_DIRECTOR = 'youth_director';
    case ECONOMAT = 'economat';

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

            self::PRESIDENT => 'Predsjednik kluba',
            self::GENERAL_DIRECTOR => 'Generalni direktor',
            self::SPORT_DIRECTOR => 'Sportski direktor',
            self::BOARD_SECRETARY => 'Sekretar upravnog odbora',
            self::CLUB_SECRETARY => 'Sekretar kluba',
            self::BOARD_MEMBER => 'Član upravnog odbora',
            self::YOUTH_DIRECTOR => 'Šef omladinskih selekcija',
            self::ECONOMAT => 'Služba ekonomata',
        };
    }

    public static function options() : array
    {
        return collect(self::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->all();
    }
}