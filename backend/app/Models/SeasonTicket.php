<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeasonTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'season',
        'holder_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(ShopOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
