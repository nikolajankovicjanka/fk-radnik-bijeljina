<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClubDocument extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_at',
        'is_published',
    ];

    protected $casts = [
        'uploaded_at' => 'date',
        'is_published' => 'boolean',
    ];

    protected $appends = [
        'file_url',
        'file_extension',
        'formatted_file_size',
    ];

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function getFileExtensionAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return strtoupper(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $size = $this->file_size;

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }

        return round($size / 1024, 2) . ' KB';
    }
}
