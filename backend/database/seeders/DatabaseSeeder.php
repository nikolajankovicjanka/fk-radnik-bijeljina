<?php

namespace Database\Seeders;

use Database\Seeders\Players\FirstTeamPlayersSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run() : void
    {
        $this->call([ClubsSeeder::class, ResultsFixturesSeeder::class, NewsSeeder::class,
                     FirstTeamPlayersSeeder::class,]);
    }
}
