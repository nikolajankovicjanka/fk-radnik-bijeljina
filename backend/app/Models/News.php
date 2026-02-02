<?php

namespace App\Models;

use App\Enums\NewsCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'category', 'published_at', 'is_active',];

    protected $casts = ['published_at' => 'datetime', 'is_active' => 'boolean', 'category' => NewsCategory::class,

                        // translatable (JSON)
                        'title'        => 'array', 'excerpt' => 'array', 'content' => 'array',];

    public function scopeActive(Builder $q) : Builder
    {
        return $q->where('is_active', true);
    }

    public function scopePublished(Builder $q) : Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Resolve translated value with fallback.
     * Accepts JSON array (casts) or plain string (legacy).
     */
    protected function tr($field, string $locale) : string
    {
        // Legacy: ako je string u bazi (stari zapis), vrati ga
        if (is_string($field)) {
            $s = trim($field);
            return $s !== '' ? $s : '';
        }

        // Ako nije array ili je null
        if (!is_array($field) || empty($field)) {
            return '';
        }

        // Fallback order: aktivni -> sr-Latn (default) -> sr-Cyrl -> en
        $fallbacks = [$locale, 'sr-Latn', 'sr-Cyrl', 'en'];

        foreach (array_unique($fallbacks) as $loc) {
            $val = $field[$loc] ?? null;
            if (is_string($val) && trim($val) !== '') {
                return trim($val);
            }
        }

        // Ako ništa od gore nije uneseno, uzmi prvi dostupan prevod (da ne bude prazno)
        foreach ($field as $val) {
            if (is_string($val) && trim($val) !== '') {
                return trim($val);
            }
        }

        return '';
    }

    public function titleFor(string $locale) : string
    {
        return $this->tr($this->title, $locale);
    }

    public function excerptFor(string $locale) : string
    {
        return $this->tr($this->excerpt, $locale);
    }

    public function contentFor(string $locale) : string
    {
        return $this->tr($this->content, $locale);
    }
}
