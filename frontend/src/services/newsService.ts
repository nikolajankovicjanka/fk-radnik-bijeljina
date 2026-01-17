import type { NewsItem, NewsCategory } from "@/types/news";

const API = import.meta.env.VITE_API_URL ?? "http://localhost:8080";

type LaravelPaginated<T> = {
    current_page: number;
    data: T[];
    per_page: number;
    total: number;
    last_page: number;
};

type ApiNews = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string | null;
    image: string | null;
    published_at: string | null;
    category?: NewsCategory;     // kad dodamo na backendu
    tags?: string[];            // kasnije
};

export async function fetchNews(params: {
    page?: number;
    perPage?: number;
    q?: string;
    category?: NewsCategory;
}) {
    const url = new URL(`${API}/api/news`);
    url.searchParams.set("per_page", String(params.perPage ?? 9));
    url.searchParams.set("page", String(params.page ?? 1));
    if (params.q) url.searchParams.set("q", params.q);
    if (params.category) url.searchParams.set("category", params.category);

    const res = await fetch(url.toString(), { headers: { Accept: "application/json" } });
    if (!res.ok) throw new Error(`Failed to fetch news: ${res.status}`);

    const json = (await res.json()) as LaravelPaginated<ApiNews>;

    const items: NewsItem[] = json.data.map((n) => ({
        id: String(n.id),
        title: n.title,
        excerpt: n.excerpt ?? "",
        slug: n.slug,
        date: n.published_at ? new Date(n.published_at).toLocaleDateString("sr-RS") : "",
        image: n.image ? `${API}/storage/${n.image}` : "/news/fk-radnik-prozivka.jpg",
        category: n.category ?? "club",
        tags: n.tags ?? undefined,
    }));

    return {
        items,
        page: json.current_page,
        perPage: json.per_page,
        total: json.total,
        lastPage: json.last_page,
    };
}

export async function getNews(perPage = 9): Promise<NewsItem[]> {
    const res = await fetchNews({ page: 1, perPage });
    return res.items;
}
