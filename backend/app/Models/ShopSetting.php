<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var(
                $setting->value,
                FILTER_VALIDATE_BOOLEAN
            ),
            'integer' => (int)$setting->value,
            'decimal', 'float' => (float)$setting->value,
            default => $setting->value,
        };
    }

    public static function setValue(
        string $key,
        mixed  $value,
        string $type = 'string'
    ): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value)
                    ? ($value ? '1' : '0')
                    : (string)$value,
                'type' => $type,
            ]
        );
    }
}
