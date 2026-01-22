import { defineStore } from 'pinia'

export type TeamType = 'first_team' | 'youth' | 'women'
export type GameStatus = 'scheduled' | 'live' | 'finished'

const API = import.meta.env.VITE_API_URL ?? 'http://localhost:8080'

type ApiClub = {
  id: number
  name: string
  slug: string
  logo: string | null
}

export type Game = {
  id: number
  team_type: TeamType
  status: GameStatus
  home_score: number | null
  away_score: number | null
  kickoff_at: string
  stadium: string | null
  round: string | null
  home_club: ApiClub
  away_club: ApiClub
}

type LaravelPaginated<T> = {
  current_page: number
  data: T[]
  per_page: number
  total: number
  last_page: number
}

export const useGamesStore = defineStore('games', {
  state: () => ({
    items: [] as Game[],
    isLoading: false,
    error: null as string | null,
    loadedAt: null as number | null, // za cache
  }),

  getters: {
    byTeam: state => (team: TeamType) => state.items.filter(g => g.team_type === team),

    lastFinished: state => (team: TeamType) => {
      const list = state.items
        .filter(
          g =>
            g.team_type === team &&
            (g.status === 'finished' || (g.home_score !== null && g.away_score !== null))
        )
        .sort((a, b) => +new Date(b.kickoff_at) - +new Date(a.kickoff_at))

      return list[0] ?? null
    },

    nextUpcoming: state => (team: TeamType) => {
      const now = new Date()
      const list = state.items
        .filter(
          g => g.team_type === team && g.status !== 'finished' && new Date(g.kickoff_at) >= now
        )
        .sort((a, b) => +new Date(a.kickoff_at) - +new Date(b.kickoff_at))

      return list[0] ?? null
    },
  },

  actions: {
    async load(perPage = 50, force = false) {
      // cache: ako je već učitano u zadnjih 60s, ne diraj
      if (!force && this.loadedAt && Date.now() - this.loadedAt < 60_000) return

      try {
        this.isLoading = true
        this.error = null

        const url = new URL(`${API}/api/games`)
        url.searchParams.set('per_page', String(perPage))

        const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } })
        if (!res.ok) throw new Error(`Failed to fetch games: ${res.status}`)

        const json = (await res.json()) as LaravelPaginated<Game>
        this.items = json.data
        this.loadedAt = Date.now()
      } catch (e: any) {
        this.error = e?.message ?? 'Failed to load games'
        this.items = []
      } finally {
        this.isLoading = false
      }
    },
  },
})
