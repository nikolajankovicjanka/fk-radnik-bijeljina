<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public function index(Request $request)
    {
        $team = $request->query('team_type');
        $perPage = (int) $request->query('per_page', 100); // OVO JE BITNO!

        $q = Player::query()->where('is_active', true);

        if ($team) {
            $q->where('team_type', $team);
        }

        return $q->orderBy('shirt_number')->paginate($perPage); // KORISTI $perPage
    }

    public function show(Player $player)
    {
        return $player;
    }
}