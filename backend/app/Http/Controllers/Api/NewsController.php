<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // GET /api/news?per_page=9&q=radnik
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 9);
        $perPage = max(1, min($perPage, 30));

        $q = trim((string) $request->query('q', ''));

        $query = News::query()
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%");
            });
        }

        $category = $request->query('category');
        if ($category) {
            $query->where('category', $category);
        }

        return $query
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    // GET /api/news/{slug}
    public function show(string $slug)
    {
        return News::query()
            ->where('is_active', true)
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();
    }
}
