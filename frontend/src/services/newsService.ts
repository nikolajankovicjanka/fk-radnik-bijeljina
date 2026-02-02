import type {NewsItem, NewsCategory} from '@/types/news'
import {i18n} from '@/i18n'
import type {SupportedLocale} from '@/translation'

const API = import.meta.env.VITE_API_URL ?? 'http://localhost:8080'

// mapiranje app locale -> browser locale (za datume)
function dateLocale(code: SupportedLocale): string {
    switch (code) {
        case 'sr-Latn':
            return 'sr-Latn-RS'
        case 'sr-Cyrl':
            return 'sr-Cyrl-RS'
        case 'en':
            return 'en-GB'
        case 'de':
            return 'de-DE'
        case 'fr':
            return 'fr-FR'
        case 'es':
            return 'es-ES'
        default:
            return 'sr-Latn-RS'
    }
}

// Accept-Language vrijednost koju šaljemo backendu
function apiLocale(): SupportedLocale {
    return (i18n.global.locale.value as SupportedLocale) ?? 'sr-Latn'
}

function categoryLabel(c?: string) {
    // ako hoćeš i ovo u prevode kasnije, prebaci na $t(...)
    switch (c) {
        case 'first_team':
            return 'PRVI TIM'
        case 'youth':
            return 'JUNIORI'
        case 'women':
            return 'ŽENSKI TIM'
        case 'club':
            return 'KLUB'
        default:
            return 'VIJESTI'
    }
}

function formatDate(iso?: string | null) {
    if (!iso) return ''
    const loc = dateLocale(apiLocale())
    return new Date(iso).toLocaleDateString(loc, {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

type LaravelPaginated<T> = {
    current_page: number
    data: T[]
    per_page: number
    total: number
    last_page: number
}

type ApiNews = {
    id: number
    title: string
    slug: string
    category: 'club' | 'first_team' | 'youth' | 'women'
    excerpt: string | null
    content: string | null
    image: string | null
    published_at: string | null
    tags?: string[]
}

function buildHeaders(extra?: Record<string, string>) {
    const lang = apiLocale()
    return {
        Accept: 'application/json',
        'Accept-Language': lang,
        ...extra,
    }
}

export async function fetchNews(params: {
    page?: number
    perPage?: number
    q?: string
    category?: NewsCategory
}) {
    const url = new URL(`${API}/api/news`)
    url.searchParams.set('per_page', String(params.perPage ?? 9))
    url.searchParams.set('page', String(params.page ?? 1))
    if (params.q) url.searchParams.set('q', params.q)
    if (params.category) url.searchParams.set('category', params.category)

    const res = await fetch(url.toString(), {headers: buildHeaders()})
    if (!res.ok) throw new Error(`Failed to fetch news: ${res.status}`)

    const json = (await res.json()) as LaravelPaginated<ApiNews>

    const items: NewsItem[] = json.data.map((n) => ({
        id: String(n.id),
        title: n.title,
        slug: n.slug,
        excerpt: n.excerpt ?? '',
        content: n.content ?? '',
        publishedAt: n.published_at ?? '',
        date: formatDate(n.published_at),
        image: n.image ? `${API}/storage/${n.image}` : '/news/fk-radnik-prozivka.jpg',
        category: (n.category ?? 'club') as any,
        categoryLabel: categoryLabel(n.category),
        tags: n.tags ?? undefined,
    }))

    return {
        items,
        page: json.current_page,
        perPage: json.per_page,
        total: json.total,
        lastPage: json.last_page,
    }
}

export async function getNews(perPage = 9): Promise<NewsItem[]> {
    const res = await fetchNews({page: 1, perPage})
    return res.items
}

export async function getNewsBySlug(slug: string): Promise<NewsItem> {
    const res = await fetch(`${API}/api/news/${encodeURIComponent(slug)}`, {
        headers: buildHeaders(),
    })

    if (!res.ok) throw new Error(`Failed to fetch news: ${res.status}`)

    const n = (await res.json()) as ApiNews

    return {
        id: String(n.id),
        title: n.title,
        slug: n.slug,
        excerpt: n.excerpt ?? '',
        content: n.content ?? '',
        publishedAt: n.published_at ?? '',
        date: formatDate(n.published_at),
        image: n.image ? `${API}/storage/${n.image}` : '/news/fk-radnik-prozivka.jpg',
        category: (n.category ?? 'club') as any,
        categoryLabel: categoryLabel(n.category),
        tags: n.tags ?? undefined,
    }
}
