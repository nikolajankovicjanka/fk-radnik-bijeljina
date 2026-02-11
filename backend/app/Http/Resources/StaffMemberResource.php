<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffMemberResource extends JsonResource
{
    public function toArray(Request $request) : array
    {
        return ['id'              => $this->id, 'team_type' => $this->team_type, 'name' => $this->name,
                'role'            => $this->role, 'photo' => $this->photo, 'photo_url' => $this->photo_url,
                'photo_thumb_url' => $this->photo_thumb_url, 'sort_order' => $this->sort_order,
                'is_active'       => (bool) $this->is_active,];
    }
}
