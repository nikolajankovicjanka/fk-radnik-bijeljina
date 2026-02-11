import {defineStore} from "pinia"
import type {StaffMember} from "@/types/staff"
import {fetchStaff} from "@/services/staffService"

export const useStaffStore = defineStore("staff", {
    state: () => ({
        items: [] as StaffMember[],
        isLoading: false,
        error: null as string | null,
        loaded: false,
    }),

    getters: {
        activeByTeam: (state) => (teamType: string) =>
            (state.items ?? [])
                .filter((s) => s.is_active && s.team_type === teamType)
                .slice()
                .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0)),
    },

    actions: {
        async load(teamType?: string) {
            if (this.loaded && !teamType) return

            this.isLoading = true
            this.error = null

            try {
                const data = await fetchStaff(teamType)
                if (!teamType) {
                    this.items = data
                    this.loaded = true
                } else {
                    const rest = this.items.filter((x) => x.team_type !== teamType)
                    this.items = [...rest, ...data]
                }
            } catch (e: any) {
                this.error = e?.message ?? "Failed to load staff"
            } finally {
                this.isLoading = false
            }
        },
    },
})
