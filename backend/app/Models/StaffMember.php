<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StaffMember extends Model
{
    protected $fillable = ['team_type', 'name', 'role', 'photo', 'sort_order', 'is_active',];

    protected $casts = ['sort_order' => 'integer', 'is_active' => 'boolean',];

    protected $appends = ['photo_url', 'photo_thumb_url',];

    public function getPhotoUrlAttribute() : ?string
    {
        if (!$this->photo) return null;
        return Storage::disk('public')->url($this->photo);
    }

    public function getPhotoThumbUrlAttribute() : ?string
    {
        if (!$this->photo) return null;
        $thumb = str_replace('.webp', '_thumb.webp', $this->photo);
        return Storage::disk('public')->url($thumb);
    }

    protected static function booted() : void
    {
        static::deleting(function (self $m) {
            if (!$m->photo) return;
            $disk = Storage::disk('public');
            $disk->delete($m->photo);
            $disk->delete(str_replace('.webp', '_thumb.webp', $m->photo));
        });

        static::updating(function (self $m) {
            if (!$m->isDirty('photo')) return;

            $old = $m->getOriginal('photo');
            if (!$old) return;

            $disk = Storage::disk('public');
            $disk->delete($old);
            $disk->delete(str_replace('.webp', '_thumb.webp', $old));
        });
    }
}
