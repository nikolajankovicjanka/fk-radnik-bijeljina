import {defineStore} from "pinia"
import type {Player, TeamType} from "@/types/player"
import {fetchPlayers} from "@/services/playersService"

export const usePlayersStore = defineStore("players", {
    state: () => ({
        items: [] as Player[],
        isLoading: false,
        error: null as string | null,
        loadedAt: null as number | null,
    }),

    getters: {
        byTeam: (state) => (team: TeamType) =>
            state.items
                .filter((p) => p.team_type === team)
                .sort((a, b) => (a.shirt_number ?? 999) - (b.shirt_number ?? 999)),

        activeByTeam: (state) => (team: TeamType) =>
            state.items
                .filter((p) => p.team_type === team && p.is_active)
                .sort((a, b) => (a.shirt_number ?? 999) - (b.shirt_number ?? 999)),
    },

    actions: {
        async load(perPage = 200, force = false) {
            if (!force && this.loadedAt && Date.now() - this.loadedAt < 60_000) return

            try {
                this.isLoading = true
                this.error = null

                const res = await fetchPlayers({perPage, active: true})
                this.items = res.items
                this.loadedAt = Date.now()
            } catch (e: any) {
                this.error = e?.message ?? "Failed to load players"
                this.items = []
            } finally {
                this.isLoading = false
            }
        },
    },
})
