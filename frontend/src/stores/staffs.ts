import {defineStore} from "pinia"
import type {StaffMember} from "@/types/staff"
import {fetchStaff} from "@/services/staffService"
import type {TeamType} from "@/types/player" // ili "@/types/game" gdje god ti je TeamType

type TeamCache = Record<TeamType, StaffMember[]>
type LoadedAtCache = Partial<Record<TeamType, number>>

export const useStaffStore = defineStore("staff", {
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
        activeByTeam: (state) => (team: TeamType) =>
            (state.itemsByTeam[team] ?? [])
                .filter((s) => s.is_active)
                .slice()
                .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0)),
    },

    actions: {
        async load(team: TeamType, force = false) {
            const loadedAt = this.loadedAtByTeam[team]
            if (!force && loadedAt && Date.now() - loadedAt < 60_000) return

            this.isLoading = true
            this.error = null

            try {
                const data = await fetchStaff(team)
                this.itemsByTeam[team] = data
                this.loadedAtByTeam[team] = Date.now()
            } catch (e: any) {
                this.error = e?.message ?? "Failed to load staff"
                this.itemsByTeam[team] = []
            } finally {
                this.isLoading = false
            }
        },
    },
})