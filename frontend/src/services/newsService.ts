// frontend/src/services/newsService.ts
import type { NewsItem, NewsCategory } from "@/types/news";
import { api } from "@/services/api";

function categoryLabel(c?: string) {
    switch (c) {
        case "first_team":
            return "PRVI TIM";
        case "youth":
            return "JUNIORI";
        case "women":
            return "ŽENSKI TIM";
        case "club":
            return "KLUB";
        default:
            return "VIJESTI";
    }
}

function formatDate(iso?: string | null) {
    if (!iso) return "";
    return new Date(iso).toLocaleDateString("sr-RS", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
}

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
    category: "club" | "first_team" | "youth" | "women";
    excerpt: string | null;
    content: string | null;
    image: string | null;
    published_at: string | null;
    tags?: string[];
};

function mapApiNewsToItem(n: ApiNews): NewsItem {
    // axios baseURL koristimo direktno preko api.defaults.baseURL
    const API = api.defaults.baseURL ?? "";

    return {
        id: String(n.id),
        title: n.title,
        slug: n.slug,

        excerpt: n.excerpt ?? "",
        content: n.content ?? "",

        publishedAt: n.published_at ?? "",
        date: formatDate(n.published_at),

        image: n.image ? `${API}/storage/${n.image}` : "/news/fk-radnik-prozivka.jpg",

        category: (n.category ?? "club") as any,
        categoryLabel: categoryLabel(n.category),

        tags: n.tags ?? undefined,
    };
}

export async function fetchNews(params: {
    page?: number;
    perPage?: number;
    q?: string;
    category?: NewsCategory;
}) {
    const { data } = await api.get<LaravelPaginated<ApiNews>>("/api/news", {
        params: {
            per_page: params.perPage ?? 9,
            page: params.page ?? 1,
            q: params.q || undefined,
            category: params.category || undefined,
        },
    });

    return {
        items: data.data.map(mapApiNewsToItem),
        page: data.current_page,
        perPage: data.per_page,
        total: data.total,
        lastPage: data.last_page,
    };
}

export async function getNews(perPage = 9): Promise<NewsItem[]> {
    const res = await fetchNews({ page: 1, perPage });
    return res.items;
}

export async function getNewsBySlug(slug: string): Promise<NewsItem> {
    const { data } = await api.get<ApiNews>(`/api/news/${encodeURIComponent(slug)}`);
    return mapApiNewsToItem(data);
}
