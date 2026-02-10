<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['team_type', 'name', 'birth_year', 'shirt_number', 'position', 'photo', 'is_active',];

    protected $casts = ['birth_year' => 'integer', 'shirt_number' => 'integer', 'is_active' => 'boolean',];

    protected $appends = ['photo_url', 'photo_thumb_url',];

    public function getPhotoUrlAttribute() : ?string
    {
        return $this->photo ? asset('storage/' . ltrim($this->photo, '/')) : null;
    }

    public function getPhotoThumbUrlAttribute() : ?string
    {
        if (!$this->photo) {
            return null;
        }

        $thumb = $this->thumbPathFromMain($this->photo);

        return asset('storage/' . ltrim($thumb, '/'));
    }

    public function thumbPathFromMain(string $mainPath) : string
    {
        // očekujemo: players/{uuid}.webp
        if (str_ends_with($mainPath, '.webp')) {
            return str_replace('.webp', '_thumb.webp', $mainPath);
        }

        // fallback (ako imaš stare jpg/png)
        return $mainPath . '_thumb';
    }
}
