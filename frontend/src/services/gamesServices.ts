import type {Game, TeamType, GameStatus, Club} from "@/types/game"

// ✅ isti origin (domen), bez localhost hardkoda
const API_BASE = ""

type LaravelPaginated<T> = {
    current_page: number
    data: T[]
    per_page: number
    total: number
    last_page: number
}

function clubLogoUrl(path?: string | null) {
    if (!path) return "/FK_Radnik_logo.png"
    if (path.startsWith("http://") || path.startsWith("https://")) return path
    return `${API_BASE}/storage/${path}` // => "/storage/..."
}

function normalizeClub(c: Club): Club {
    return {
        ...c,
        logo: c.logo ? clubLogoUrl(c.logo) : null,
    }
}

export async function fetchGames(params?: {
    page?: number
    perPage?: number
    team_type?: TeamType
    status?: GameStatus
    order?: "asc" | "desc"
}) {
    // ✅ new URL mora imati base
    const url = new URL("/api/games", window.location.origin)

    url.searchParams.set("page", String(params?.page ?? 1))
    url.searchParams.set("per_page", String(params?.perPage ?? 50))
    if (params?.team_type) url.searchParams.set("team_type", params.team_type)
    if (params?.status) url.searchParams.set("status", params.status)
    if (params?.order) url.searchParams.set("order", params.order)

    const res = await fetch(url.toString(), {headers: {Accept: "application/json"}})
    if (!res.ok) throw new Error(`Failed to fetch games: ${res.status}`)

    const json = (await res.json()) as LaravelPaginated<Game>

    const items = json.data.map((g) => ({
        ...g,
        home_club: normalizeClub(g.home_club),
        away_club: normalizeClub(g.away_club),
    }))

    return {
        items,
        page: json.current_page,
        perPage: json.per_page,
        total: json.total,
        lastPage: json.last_page,
    }
}
