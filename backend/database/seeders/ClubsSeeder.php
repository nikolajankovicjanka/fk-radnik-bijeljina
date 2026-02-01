<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClubsSeeder extends Seeder
{
    public function run() : void
    {
        $clubs = ['FK Radnik Soccerbet', 'FK Sarajevo', 'FK Željezničar', 'FK Borac Banja Luka', 'HŠK Zrinjski',
                  'HŠK Posušje', 'FK Velež', 'FK Sloga Doboj', 'FK Rudar Prijedor', 'NK Široki Brijeg', 'Kruševo',];

        foreach ($clubs as $name) {
            Club::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
