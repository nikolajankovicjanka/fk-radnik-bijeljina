<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffMemberResource;
use App\Models\StaffMember;
use Illuminate\Http\Request;

class StaffMemberController extends Controller
{
    public function index(Request $request)
    {
        $q = StaffMember::query();

        if ($request->filled('team_type')) {
            $q->where('team_type', $request->string('team_type')->toString());
        }

        // default: samo aktivni
        $active = $request->input('active', 1);
        if ($active !== null) {
            $q->where('is_active', (bool) $active);
        }

        $items = $q->orderBy('team_type')->orderBy('sort_order')->orderBy('name')->get();

        return StaffMemberResource::collection($items);
    }
}
