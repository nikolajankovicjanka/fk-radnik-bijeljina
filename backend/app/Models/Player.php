<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['team_type', 'name', 'birth_year', 'shirt_number', 'position', 'photo', 'is_active',];

    protected $casts = ['birth_year' => 'integer', 'shirt_number' => 'integer', 'is_active' => 'boolean',];
}
