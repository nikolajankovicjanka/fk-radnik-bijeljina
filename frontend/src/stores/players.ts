import {defineStore} from "pinia"
import type {Player, TeamType} from "@/types/player"
import {fetchPlayers} from "@/services/playersService"

type LoadParams = {
    perPage?: number
    team_type: TeamType
    active?: boolean
}

type TeamCache = Record<TeamType, Player[]>
type LoadedAtCache = Partial<Record<TeamType, number>>

export const usePlayersStore = defineStore("players", {
    state: () => ({
        itemsByTeam: {
            first_team: [],
            youth: [],
            women: [],
        } as TeamCache,

        isLoading: false,
        error: null as string | null,

        loadedAtByTeam: {} as LoadedAtCache,
    }),

    getters: {
        byTeam: (state) => (team: TeamType) =>
            (state.itemsByTeam[team] ?? [])
                .slice()
                .sort((a, b) => (a.shirt_number ?? 999) - (b.shirt_number ?? 999)),

        activeByTeam: (state) => (team: TeamType) =>
            (state.itemsByTeam[team] ?? [])
                .filter((p) => p.is_active)
                .slice()
                .sort((a, b) => (a.shirt_number ?? 999) - (b.shirt_number ?? 999)),
    },

    actions: {
        async load(params: LoadParams, force = false) {
            const perPage = params.perPage ?? 200
            const active = params.active ?? true
            const team = params.team_type

            const loadedAt = this.loadedAtByTeam[team]
            if (!force && loadedAt && Date.now() - loadedAt < 60_000) return

            try {
                this.isLoading = true
                this.error = null

                const res = await fetchPlayers({
                    perPage,
                    active,
                    team_type: team
                })

                // ključ: NE prepisuj sve, nego samo taj tim
                this.itemsByTeam[team] = res.items
                this.loadedAtByTeam[team] = Date.now()
            } catch (e: any) {
                this.error = e?.message ?? "Failed to load players"
                this.itemsByTeam[team] = []
            } finally {
                this.isLoading = false
            }
        },
    },
})