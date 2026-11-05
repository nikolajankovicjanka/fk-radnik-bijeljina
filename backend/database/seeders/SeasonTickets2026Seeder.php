<?php

namespace Database\Seeders;

use App\Models\SeasonTicket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SeasonTickets2026Seeder extends Seeder
{
    public function run(): void
    {
        $season = '2026/27';

        // Brojevi su preuzeti iz kolone "broj karte" iz dostavljenog spiska.
        // Red sa nepoznatim brojem (?) namjerno je izostavljen.
        // Nema automatskog dodavanja vodecih nula.
        $tickets = [
            ['2', 'Stanko Nešković'],
            ['3', 'Srđan Nikolić'],
            ['27', 'Dušan Rikanović'],
            ['33', 'Čordašević Miloš'],
            ['38', 'Želimir Rudeš'],
            ['40', 'Dragan Perić'],
            ['41', 'Igor Lovrić'],
            ['42', 'Dragan Đurić'],
            ['43', 'Aleksandar Dragutinović'],
            ['44', 'Huso Sadiković'],
            ['45', 'Milan Predić'],
            ['46', 'Milan Petrović'],
            ['47', 'Vladimir Močević'],
            ['48', 'Marko Cvijetić'],
            ['49', 'Miloš Đokić'],
            ['50', 'Dragan Janković'],
            ['51', 'Miodrag Perić'],
            ['52', 'Aleksandar Nikolić'],
            ['53', 'Slađan Gnjatić'],
            ['54', 'Ignjat Simić'],
            ['55', 'Goran Marković'],
            ['56', 'Zlatko Simić'],
            ['57', 'Dimitrije Mrkajić'],
            ['58', 'Ratko Simić'],
            ['59', 'Milan Trninić'],
            ['60', 'Milan Trninić'],
            ['68', 'Dražen Nikolić'],
            ['69', 'Muhamed Merić'],
            ['70', 'Miroslav Golubović'],
            ['71', 'Ostoja Lazarević'],
            ['72', 'Darko Radić'],
            ['75', 'Ilijaz Miralemović'],
            ['76', 'Momčilo Cvijetić'],
            ['100', 'Maja Nedaković'],
            ['105', 'Milorad Sofrenić'],
            ['199', 'Uroš Sredojević'],
            ['8', 'Igor Samardžić'],
            ['9', 'Mladen Vulin'],
            ['10', 'Gajić Zoran'],
            ['11', 'Igor Samardžić'],
            ['34', 'Aleksandar Todorović'],
            ['98', 'Jaćimović doo - Juventa'],
            ['99', 'Jaćimović doo - Juventa'],
            ['101', 'Jaćimović doo - Juventa'],
            ['103', 'Jaćimović doo - Juventa'],
            ['102', 'Jaćimović doo - Juventa'],
            ['94', 'Tehnika inženjering doo Nešo Gajić'],
            ['95', 'Tehnika inženjering doo Nešo Gajić'],
            ['96', 'Tehnika inženjering doo Nešo Gajić'],
            ['97', 'Tehnika inženjering doo Nešo Gajić'],
            ['63', 'Viktor Aleksić'],
            ['64', 'Viktor Aleksić'],
            ['65', 'Viktor Aleksić'],
            ['66', 'Goran Knežević'],
            ['67', 'Goran Knežević'],
            ['4', 'Slavko Pantić - Pass'],
            ['5', 'Branislav Nikolić - Pass'],
            ['6', 'Mladen Maletić - Pass'],
            ['13', 'Jovica marković - Pass'],
            ['14', 'Goran Vitomir - Pass'],
            ['15', 'Mega drvo'],
            ['16', 'Mega drvo'],
            ['17', 'Mega drvo'],
            ['18', 'Mega drvo'],
            ['19', 'Mega drvo'],
            ['20', 'Mega drvo'],
            ['21', 'Naša Banka'],
            ['22', 'Naša Banka'],
            ['23', 'Naša Banka'],
            ['24', 'Naša Banka'],
            ['25', 'Naša Banka'],
            ['26', 'Naša Banka'],
            ['28', 'Naša Banka'],
            ['29', 'Naša Banka'],
            ['30', 'Naša Banka'],
            ['31', 'Naša Banka'],
            ['32', 'Goran'],
            ['35', 'Jelen pivo'],
            ['36', 'Jelen pivo'],
            ['37', 'Jelen pivo'],
            ['39', 'Vitinka - Siniša'],
            ['61', 'Vitinka - Siniša'],
            ['62', 'Vitinka - Siniša'],
        ];

        DB::transaction(function () use ($season, $tickets): void {
            $numbers = array_column($tickets, 0);

            // ticket_number je globalno UNIQUE; nikada ne premjestati
            // postojecu karticu iz druge sezone bez rucne odluke.
            $conflicts = SeasonTicket::query()
                ->whereIn('ticket_number', $numbers)
                ->where('season', '!=', $season)
                ->pluck('ticket_number')
                ->all();

            if ($conflicts !== []) {
                throw new RuntimeException(
                    'Brojevi vec postoje u drugoj sezoni: ' . implode(', ', $conflicts)
                );
            }

            $created = 0;
            $existing = 0;

            foreach ($tickets as [$number, $holder]) {
                $ticket = SeasonTicket::query()->firstOrCreate(
                    ['ticket_number' => $number],
                    [
                        'season' => $season,
                        'holder_name' => $holder,
                        'is_active' => true,
                    ]
                );

                // Postojece kartice ostaju neizmijenjene, ukljucujuci
                // status deaktivacije i ime koje je uredjeno u Filamentu.
                $ticket->wasRecentlyCreated ? $created++ : $existing++;
            }

            $this->command?->info(
                "Sezonske karte {$season}: dodato {$created}, vec postojalo {$existing}."
            );
        });
    }
}
