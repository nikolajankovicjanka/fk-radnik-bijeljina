<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'slug', 'logo'];

    public function homeGames()
    {
        return $this->hasMany(Game::class, 'home_club_id');
    }

    public function awayGames()
    {
        return $this->hasMany(Game::class, 'away_club_id');
    }
}
