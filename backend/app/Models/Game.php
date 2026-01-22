<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'team_type',
        'home_club_id',
        'away_club_id',
        'home_score',
        'away_score',
        'kickoff_at',
        'status',
        'stadium',
        'round',
    ];

    protected $casts = [
        'kickoff_at' => 'datetime',
    ];

    public function homeClub()
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub()
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }
}
