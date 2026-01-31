<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\NewsSeeder;
use Database\Seeders\Players\FirstTeamPlayersSeeder;
use Database\Seeders\ResultsFixturesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run() : void
    {
        $this->call([NewsSeeder::class, FirstTeamPlayersSeeder::class, ResultsFixturesSeeder::class,]);
    }
}
