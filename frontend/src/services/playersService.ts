import type {Player, TeamType} from "@/types/player"

const API = ""

type LaravelPaginated<T> = {
    current_page: number
    data: T[]
    per_page: number
    total: number
    last_page: number
}

function playerPhotoUrl(path?: string | null) {
    if (!path) return "/players/placeholder.png"
    return `${API}/storage/${path}`
}

export async function fetchPlayers(params?: {
    page?: number
    perPage?: number
    team_type?: TeamType
    active?: boolean
}) {
    const url = new URL("/api/players", window.location.origin)
    url.searchParams.set("page", String(params?.page ?? 1))
    url.searchParams.set("per_page", String(params?.perPage ?? 100))
    if (params?.team_type) url.searchParams.set("team_type", params.team_type)
    if (typeof params?.active === "boolean") url.searchParams.set("active", params.active ? "1" : "0")

    const res = await fetch(url.toString(), {headers: {Accept: "application/json"}})
    if (!res.ok) throw new Error(`Failed to fetch players: ${res.status}`)

    const json = (await res.json()) as LaravelPaginated<Player>

    // Normalizuj photo u full URL da komponente ne brinu o storage putanji
    const items = json.data.map((p) => ({
        ...p,
        photo: p.photo ? playerPhotoUrl(p.photo) : null,
    }))

    return {
        items,
        page: json.current_page,
        perPage: json.per_page,
        total: json.total,
        lastPage: json.last_page,
    }
}
