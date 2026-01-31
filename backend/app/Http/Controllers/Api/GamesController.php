<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 200));

        $teamType = $request->query('team_type');   // first_team|youth|women
        $status = $request->query('status');      // scheduled|live|finished
        $order = strtolower((string) $request->query('order', 'desc')); // asc|desc
        $order = in_array($order, ['asc', 'desc'], true) ? $order : 'desc';

        return Game::query()->with(['homeClub',
                                    'awayClub'])->when($teamType, fn($q) => $q->where('team_type', $teamType))->when($status, fn($q) => $q->where('status', $status))->orderBy('kickoff_at', $order)->paginate($perPage);
    }

    public function show($id)
    {
        return Game::query()->with(['homeClub', 'awayClub'])->findOrFail($id);
    }
}
