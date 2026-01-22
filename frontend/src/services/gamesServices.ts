// stores/games.ts
import {defineStore} from 'pinia'
import type {Game, TeamType} from '@/types/game'
import {fetchGames} from '@/services/gamesServices'

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
        loadedAt: null as number | null,
        pagination: {
            page: 1,
            perPage: 50,
            total: 0,
            lastPage: 1,
        },
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
            if (!force && this.loadedAt && Date.now() - this.loadedAt < 60_000) {
                console.log('Games cache hit, skipping load')
                return
            }

            try {
                this.isLoading = true
                this.error = null

                // KORISTI SERVICE umjesto direktnog fetch!
                const response = await fetchGames({perPage})

                this.items = response.data
                this.pagination = {
                    page: response.current_page,
                    perPage: response.per_page,
                    total: response.total,
                    lastPage: response.last_page,
                }
                this.loadedAt = Date.now()

                console.log('Games loaded successfully:', this.items.length)
            } catch (e: any) {
                this.error = e?.message ?? 'Failed to load games'
                this.items = []
                console.error('Error loading games:', e)
            } finally {
                this.isLoading = false
            }
        },
    },
})
