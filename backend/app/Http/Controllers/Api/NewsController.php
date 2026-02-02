<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // GET /api/news?per_page=9&q=radnik&category=club
    public function index(Request $request)
    {
        $locale = (string) $request->header('Accept-Language', 'sr-Latn');

        $perPage = (int) $request->query('per_page', 9);
        $perPage = max(1, min($perPage, 30));

        $q = trim((string) $request->query('q', ''));

        $query = News::query()->active()->published();

        // Filter by category (tvoj enum cast ostaje OK)
        $category = $request->query('category');
        if ($category) {
            $query->where('category', $category);
        }

        // Search (q) po aktivnom jeziku + fallback na sr-Latn i en
        if ($q !== '') {
            $localesToSearch = array_values(array_unique([$locale, 'sr-Latn', 'en']));

            $query->where(function ($sub) use ($q, $localesToSearch) {
                foreach ($localesToSearch as $loc) {
                    $path = '$."' . str_replace('"', '\"', $loc) . '"';

                    $sub->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, ?)) LIKE ?", [$path, "%{$q}%"]);

                    $sub->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(excerpt, ?)) LIKE ?", [$path, "%{$q}%"]);
                }
            });
        }

        $paginator = $query->orderByDesc('published_at')->paginate($perPage);

        // Transform paginated collection: title/excerpt kao string
        $paginator->getCollection()->transform(function (News $n) use ($locale) {
            return ['id'        => $n->id, 'title' => $n->titleFor($locale), 'slug' => $n->slug,
                    'excerpt'   => $n->excerptFor($locale), 'content' => null,
                    // list view ne treba content (štedi payload)
                    'image'     => $n->image, 'category' => $n->category, 'published_at' => $n->published_at,
                    'is_active' => $n->is_active, 'created_at' => $n->created_at, 'updated_at' => $n->updated_at,];
        });

        return $paginator;
    }

    // GET /api/news/{slug}
    public function show(Request $request, string $slug)
    {
        $locale = (string) $request->header('Accept-Language', 'sr-Latn');

        $n = News::query()->active()->published()->where('slug', $slug)->firstOrFail();

        return response()->json(['id'           => $n->id, 'title' => $n->titleFor($locale), 'slug' => $n->slug,
                                 'excerpt'      => $n->excerptFor($locale), 'content' => $n->contentFor($locale),
                                 'image'        => $n->image, 'category' => $n->category,
                                 'published_at' => $n->published_at, 'is_active' => $n->is_active,
                                 'created_at'   => $n->created_at, 'updated_at' => $n->updated_at,]);
    }
}
