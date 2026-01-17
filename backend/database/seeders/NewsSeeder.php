<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        News::create([
            'title' => 'Prva vijest FK Radnik',
            'slug' => 'prva-vijest-fk-radnik',
            'excerpt' => 'Kratak opis za homepage karticu.',
            'content' => 'Puni tekst vijesti...',
            'image' => null,
            'is_active' => true,
            'published_at' => now(),
        ]);
    }
}
