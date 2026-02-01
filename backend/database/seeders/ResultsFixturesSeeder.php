<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResultsFixturesSeeder extends Seeder
{
    public function run() : void
    {
        $teamType = 'first_team';
        $nameMap = ['FK RADNIK'           => 'FK Radnik Soccerbet', 'FK Radnik' => 'FK Radnik Soccerbet',
                    'FK RADNIK SOCCERBET' => 'FK Radnik Soccerbet', 'FK Radnik Soccerbet' => 'FK Radnik Soccerbet',
                    'Željezničar'         => 'FK Željezničar', 'FK Željezničar' => 'FK Željezničar',

                    'Posušje' => 'HŠK Posušje', 'HŠK Posušje' => 'HŠK Posušje',

                    'Zrinjski' => 'HŠK Zrinjski', 'HŠK Zrinjski' => 'HŠK Zrinjski',

                    'Velež' => 'FK Velež', 'FK Velež' => 'FK Velež',

                    'Rudar Prijedor' => 'FK Rudar Prijedor', 'FK Rudar Prijedor' => 'FK Rudar Prijedor',

                    'Borac Banja Luka' => 'FK Borac Banja Luka', 'FK BORAC' => 'FK Borac Banja Luka',
                    'FK Borac'         => 'FK Borac Banja Luka', 'FK Borac Banja Luka' => 'FK Borac Banja Luka',

                    'Sloga Doboj'    => 'FK Sloga Doboj', 'FK SLOGA DOBOJ' => 'FK Sloga Doboj',
                    'FK Sloga Doboj' => 'FK Sloga Doboj',

                    'Široki Brijeg' => 'NK Široki Brijeg', 'NK Široki Brijeg' => 'NK Široki Brijeg',

                    'FK Sarajevo' => 'FK Sarajevo', 'FK SARAJEVO' => 'FK Sarajevo',

                    'Krusevo' => 'Kruševo', 'Kruševo' => 'Kruševo',];
        $finished = [['1', '2025-07-27 21:00', 'FK ŽELJEZNIČAR', 'FK RADNIK', 1, 1, 'Stadion "Grbavica" Sarajevo',
                      'WWin Liga BiH'],
                     ['2', '2025-08-03 18:45', 'FK SARAJEVO', 'FK RADNIK', 4, 4, 'Azsim Ferhatovic Hase',
                      'WWin Liga BiH'],
                     ['3', '2025-08-10 18:45', 'FK RADNIK', 'HŠK POSUŠJE', 2, 0, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['4', '2025-08-15 21:00', 'FK RUDAR PRIJEDOR', 'FK RADNIK', 1, 0, 'Rudar Prijedor',
                      'WWin Liga BiH'],
                     ['5', '2025-08-23 21:00', 'FK RADNIK', 'FK VELEŽ', 3, 0, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['6', '2025-09-01 18:00', 'HŠK ZRINJSKI', 'FK RADNIK', 2, 0, 'Stadion "HŠK Zrinjski" Mostar',
                      'WWin Liga BiH'],
                     ['7', '2025-09-12 21:00', 'FK RADNIK', 'FK SLOGA DOBOJ', 0, 0, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['8', '2025-09-20 20:45', 'FK BORAC', 'FK RADNIK', 5, 1, 'Gradski stadion Banja Luka',
                      'WWin Liga BiH'],
                     ['9', '2025-09-26 18:00', 'FK RADNIK', 'NK ŠIROKI BRIJEG', 0, 0, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['10', '2025-10-05 17:00', 'FK RADNIK', 'FK ŽELJEZNIČAR', 1, 0, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['11', '2025-10-18 18:30', 'FK RADNIK', 'FK SARAJEVO', 2, 2, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['12', '2025-10-26 15:30', 'HŠK POSUŠJE', 'FK RADNIK', 4, 0, 'Stadion "Mokri Dolac"',
                      'WWin Liga BiH'],
                     ['13', '2025-10-31 18:00', 'FK RADNIK', 'FK RUDAR PRIJEDOR', 2, 1, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['14', '2025-11-09 17:30', 'FK VELEŽ', 'FK RADNIK', 1, 0, 'Stadion "Rođeni"', 'WWin Liga BiH'],
                     ['15', '2025-11-21 16:00', 'FK RADNIK', 'HŠK ZRINJSKI', 0, 2, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['16', '2025-11-28 18:00', 'FK SLOGA DOBOJ', 'FK RADNIK', 2, 1, 'Stadion "Luke"', 'WWin Liga BiH'],
                     ['17', '2025-12-03 16:00', 'FK RADNIK', 'FK BORAC', 0, 1, 'Gradski stadion u Bijeljini',
                      'WWin Liga BiH'],
                     ['18', '2025-12-08 16:00', 'NK ŠIROKI BRIJEG', 'FK RADNIK', 1, 3, 'Stadion "Pecara" Široki Brijeg',
                      'WWin Liga BiH'],
                     ['19', '2025-12-13 16:00', 'FK ŽELJEZNIČAR', 'FK RADNIK', 0, 0, 'Stadion "Grbavica" Sarajevo',
                      'WWin Liga BiH'],];

        foreach ($finished as [$round, $kickoffAt, $homeRaw, $awayRaw, $hs, $as, $stadium, $competition]) {
            $homeName = $this->canonName($homeRaw, $nameMap);
            $awayName = $this->canonName($awayRaw, $nameMap);

            $homeClub = $this->findOrCreateClub($homeName);
            $awayClub = $this->findOrCreateClub($awayName);

            $dt = Carbon::createFromFormat('Y-m-d H:i', $kickoffAt, 'Europe/Sarajevo');

            Game::updateOrCreate(['team_type'    => $teamType, 'home_club_id' => $homeClub->id,
                                  'away_club_id' => $awayClub->id,
                                  'kickoff_at'   => $dt->format('Y-m-d H:i:s'),], ['status'      => 'finished',
                                                                                   'home_score'  => $hs,
                                                                                   'away_score'  => $as,
                                                                                   'stadium'     => $stadium,
                                                                                   'round'       => $round,
                                                                                   'competition' => $competition,]);
        }
        $upcoming = [['2026-02-06 20:00', 'FK Sarajevo', 'FK Radnik Soccerbet', 'Asim Ferhatović Hase', 'Kup BiH',
                      'runda 20'],

                     ['2026-02-11 15:30', 'FK Radnik Soccerbet', 'Krusevo', 'Gradski stadion Bijeljina', 'Kup BiH',
                      '1/8 finala'], ['2026-02-14 17:00', 'FK Radnik Soccerbet', 'Posušje', 'Gradski stadion Bijeljina',
                                      'WWin Liga BiH', 'runda 21'],
                     ['2026-02-21 17:00', 'Rudar Prijedor', 'FK Radnik Soccerbet', 'Stadion Rudara', 'WWin Liga BiH',
                      'runda 22'],
                     ['2026-02-28 17:00', 'FK Radnik Soccerbet', 'Velež', 'Gradski stadion Bijeljina', 'WWin Liga BiH',
                      'runda 23'],
                     ['2026-03-07 17:00', 'Zrinjski', 'FK Radnik Soccerbet', 'Stadion pod Bijelim Brijegom',
                      'WWin Liga BiH', 'runda 24'],
                     ['2026-03-14 17:00', 'FK Radnik Soccerbet', 'Sloga Doboj', 'Gradski stadion Bijeljina',
                      'WWin Liga BiH', 'runda 25'],
                     ['2026-03-21 17:00', 'Borac Banja Luka', 'FK Radnik Soccerbet', 'Gradski stadion Banja Luka',
                      'WWin Liga BiH', 'runda 26'],
                     ['2026-04-04 17:00', 'FK Radnik Soccerbet', 'Široki Brijeg', 'Gradski stadion Bijeljina',
                      'WWin Liga BiH', 'runda 27'],
                     ['2026-04-11 17:00', 'FK Radnik Soccerbet', 'Željezničar', 'Gradski stadion Bijeljina',
                      'WWin Liga BiH', 'runda 28'],
                     ['2026-04-18 17:00', 'FK Radnik Soccerbet', 'FK Sarajevo', 'Gradski stadion Bijeljina',
                      'WWin Liga BiH', 'runda 29'],
                     ['2026-04-22 17:00', 'Posušje', 'FK Radnik Soccerbet', 'Stadion Mokri Dolac', 'WWin Liga BiH',
                      'runda 30'],
                     ['2026-04-25 17:00', 'FK Radnik Soccerbet', 'Rudar Prijedor', 'Gradski stadion Bijeljina',
                      'WWin Liga BiH', 'runda 31'],
                     ['2026-05-02 17:00', 'Velež', 'FK Radnik Soccerbet', 'Stadion Rođeni', 'WWin Liga BiH',
                      'runda 32'], ['2026-05-09 17:00', 'FK Radnik Soccerbet', 'Zrinjski', 'Gradski stadion Bijeljina',
                                    'WWin Liga BiH', 'runda 33'],
                     ['2026-05-17 17:00', 'Sloga Doboj', 'FK Radnik Soccerbet', 'Stadion Luke', 'WWin Liga BiH',
                      'runda 34'],
                     ['2026-05-24 17:00', 'FK Radnik Soccerbet', 'Borac Banja Luka', 'Gradski stadion Bijeljina',
                      'WWin Liga BiH', 'runda 35'],
                     ['2026-05-31 17:00', 'Široki Brijeg', 'FK Radnik Soccerbet', 'Stadion Pecara', 'WWin Liga BiH',
                      'runda 36'],];

        foreach ($upcoming as [$kickoffAt, $homeRaw, $awayRaw, $stadium, $competition, $round]) {
            $homeName = $this->canonName($homeRaw, $nameMap);
            $awayName = $this->canonName($awayRaw, $nameMap);

            $homeClub = $this->findOrCreateClub($homeName);
            $awayClub = $this->findOrCreateClub($awayName);

            $dt = Carbon::createFromFormat('Y-m-d H:i', $kickoffAt, 'Europe/Sarajevo');

            // Ako jednog dana dodaš skorove za kup (finished), samo će ih updateovati.
            $status = 'scheduled';

            Game::updateOrCreate(['team_type'    => $teamType, 'home_club_id' => $homeClub->id,
                                  'away_club_id' => $awayClub->id,
                                  'kickoff_at'   => $dt->format('Y-m-d H:i:s'),], ['status'      => $status,
                                                                                   'home_score'  => null,
                                                                                   'away_score'  => null,
                                                                                   'stadium'     => $stadium,
                                                                                   'round'       => $round,
                                                                                   'competition' => $competition,]);
        }
    }

    private function canonName(string $raw, array $map) : string
    {
        $raw = trim(preg_replace('/\s+/', ' ', $raw));
        if (isset($map[$raw])) return $map[$raw];

        // fallback: title-case + sačuvaj FK/NK/HŠK
        $parts = explode(' ', mb_strtolower($raw));
        $out = array_map(function ($p) {
            $upper = mb_strtoupper($p);
            if (in_array($upper, ['FK', 'NK', 'HŠK'], true)) return $upper;
            return mb_strtoupper(mb_substr($p, 0, 1)) . mb_substr($p, 1);
        }, $parts);

        return implode(' ', $out);
    }

    private function findOrCreateClub(string $name) : Club
    {
        $existing = Club::query()->where('name', $name)->first();
        if ($existing) return $existing;

        $slug = Str::slug($name);

        return Club::firstOrCreate(['slug' => $slug], ['name' => $name, 'logo' => null]);
    }
}
