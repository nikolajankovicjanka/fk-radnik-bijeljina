<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function index(Request $request)
    {
        return Game::query()
            ->with(['homeClub', 'awayClub'])
            ->orderByDesc('kickoff_at')
            ->paginate((int) $request->query('per_page', 10));
    }

    public function show($id)
    {
        return Game::query()
            ->with(['homeClub', 'awayClub'])
            ->findOrFail($id);
    }
}