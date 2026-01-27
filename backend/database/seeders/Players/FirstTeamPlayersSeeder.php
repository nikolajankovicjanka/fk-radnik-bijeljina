<?php

namespace Database\Seeders\Players;

use Illuminate\Database\Seeder;
use App\Models\Player;

class FirstTeamPlayersSeeder extends Seeder
{
    public function run() : void
    {
        $defaultPhoto = 'players/default.png';

        $players = [// IMAJU PHOTO (iz Filamenta)
                    ['shirt_number' => 6, 'position' => 'CB', 'birth_year' => 1995, 'name' => 'Djordje Cosic',
                     'photo'        => 'players/01KFKVGAEB62HKJ71734K688WR.png',],
                    ['shirt_number' => 7, 'position' => 'RM', 'birth_year' => 2002, 'name' => 'Sami Faraj',
                     'photo'        => 'players/01KFKVY4QVJ7P0TSRNN67QWFEY.png',],
                    ['shirt_number' => 10, 'position' => 'CM', 'birth_year' => 2000, 'name' => 'Emanuel Crnko',
                     'photo'        => 'players/01KFM2ZE5J865PCM2Q4R2EN961.png',],
                    ['shirt_number' => 12, 'position' => 'FC', 'birth_year' => 2006, 'name' => 'Ognjen Klenpic',
                     'photo'        => 'players/01KG0FF1GXAGCFZQ1CVXXSGWJP.png',],
                    ['shirt_number' => 14, 'position' => 'RB', 'birth_year' => 2002, 'name' => 'Andrija Markovic',
                     'photo'        => 'players/01KG0FJA2VA1XCB4QXV3GYVAZM.png',],
                    ['shirt_number' => 15, 'position' => 'CB', 'birth_year' => 2007, 'name' => 'Nikola Djuric',
                     'photo'        => 'players/01KFM2X2VYS0Y4YZYX2H619GV8.png',],
                    ['shirt_number' => 16, 'position' => 'RB', 'birth_year' => 2007, 'name' => 'Bozidar Dimitric',
                     'photo'        => 'players/01KFKVZCMRQKFHYB662WJ6TTKD.png',],
                    ['shirt_number' => 17, 'position' => 'GK', 'birth_year' => 1998, 'name' => 'Elvir Trako',
                     'photo'        => 'players/01KG0D9Z4XDWXWGEF85F294VVC.png',],
                    ['shirt_number' => 24, 'position' => 'LB', 'birth_year' => 2000, 'name' => 'Marko Čubrilo',
                     'photo'        => 'players/01KG0FKNGAP6A68VGZ4ZRXPSJ6.png',],
                    ['shirt_number' => 77, 'position' => 'LM', 'birth_year' => 2001, 'name' => 'Mohamed Ghorzi',
                     'photo'        => 'players/01KFKVDX2ZGKQS2Z8RHSGV50JX.png',],

                    // NEMAJU PHOTO → DEFAULT
                    ['shirt_number' => 1, 'position' => 'GK', 'birth_year' => 1998, 'name' => 'Dusan Markovic'],
                    ['shirt_number' => 2, 'position' => 'CB', 'birth_year' => 2000, 'name' => 'Bartul Markovina'],
                    ['shirt_number' => 33, 'position' => 'LB', 'birth_year' => 1998, 'name' => 'Lazar Vukovic'],
                    ['shirt_number' => 3, 'position' => 'LB', 'birth_year' => 1993, 'name' => 'Slaviša Radović'],
                    ['shirt_number' => 26, 'position' => 'CB', 'birth_year' => 1995, 'name' => 'Đorđe Ćosić'],
                    ['shirt_number' => 6, 'position' => 'DM', 'birth_year' => 2006, 'name' => 'Felix Agzemang'],
                    ['shirt_number' => 11, 'position' => 'AM', 'birth_year' => 1998, 'name' => 'Aldin Hrvanović'],
                    ['shirt_number' => 30, 'position' => 'AM', 'birth_year' => 2001, 'name' => 'Ateef Konate'],
                    ['shirt_number' => 31, 'position' => 'AM', 'birth_year' => 2001, 'name' => 'Tino Blaž Lauš'],
                    ['shirt_number' => 22, 'position' => 'LM', 'birth_year' => 2005, 'name' => 'Andrija Lošić'],
                    ['shirt_number' => 25, 'position' => 'LM', 'birth_year' => 2006, 'name' => 'Andrej Krstić'],
                    ['shirt_number' => 29, 'position' => 'FC', 'birth_year' => 2002, 'name' => 'Danilo Teodorovic'],
                    ['shirt_number' => 99, 'position' => 'FC', 'birth_year' => 2005, 'name' => 'Antoine Ijoma'],
                    ['shirt_number' => 20, 'position' => 'FC', 'birth_year' => 2006, 'name' => 'Ognjen Klenpić'],];

        foreach ($players as $p) {
            Player::updateOrCreate(['team_type'    => 'first_team',
                                    'shirt_number' => (int) $p['shirt_number'],], ['name'       => $p['name'],
                                                                                   'birth_year' => (int) $p['birth_year'],
                                                                                   'position'   => $p['position'],
                                                                                   'photo'      => $p['photo'] ?? $defaultPhoto,
                                                                                   'is_active'  => true,]);
        }
    }
}
