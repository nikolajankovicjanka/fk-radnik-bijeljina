import {defineStore} from "pinia"
import type {Game, TeamType} from "@/types/game"
import {fetchGames} from "@/services/gamesServices"

type Pagination = {
    page: number
    perPage: number
    total: number
    lastPage: number
}

const TEAMS: TeamType[] = ["first_team", "youth", "women"]

export const useGamesStore = defineStore("games", {
    state: () => ({
        finishedByTeam: {
            first_team: [] as Game[],
            youth: [] as Game[],
            women: [] as Game[],
        } as Record<TeamType, Game[]>,

        scheduledByTeam: {
            first_team: [] as Game[],
            youth: [] as Game[],
            women: [] as Game[],
        } as Record<TeamType, Game[]>,

        finishedPaginationByTeam: {
            first_team: {page: 1, perPage: 5, total: 0, lastPage: 1},
            youth: {page: 1, perPage: 5, total: 0, lastPage: 1},
            women: {page: 1, perPage: 5, total: 0, lastPage: 1},
        } as Record<TeamType, Pagination>,

        isLoadingFinishedByTeam: {
            first_team: false,
            youth: false,
            women: false,
        } as Record<TeamType, boolean>,

        isLoadingScheduledByTeam: {
            first_team: false,
            youth: false,
            women: false,
        } as Record<TeamType, boolean>,

        error: null as string | null,

        // ✅ dedup za paralelne pozive
        _inFlight: new Map<string, Promise<any>>(),
    }),

    getters: {
        items: (state) => {
            const all = [
                ...state.finishedByTeam.first_team,
                ...state.finishedByTeam.youth,
                ...state.finishedByTeam.women,
                ...state.scheduledByTeam.first_team,
                ...state.scheduledByTeam.youth,
                ...state.scheduledByTeam.women,
            ]
            const map = new Map<number, Game>()
            for (const g of all) map.set(g.id, g)
            return Array.from(map.values())
        },
        lastFinished: (state) => (team: TeamType) => {
            const list = [...(state.finishedByTeam[team] ?? [])].sort(
                (a, b) => +new Date(b.kickoff_at) - +new Date(a.kickoff_at)
            )
            return list[0] ?? null
        },
        nextUpcoming: (state) => (team: TeamType) => {
            const now = new Date()
            const list = (state.scheduledByTeam[team] ?? [])
                .filter((g) => new Date(g.kickoff_at) >= now)
                .sort((a, b) => +new Date(a.kickoff_at) - +new Date(b.kickoff_at))

            return list[0] ?? null
        },
        upcomingAll: (state) => (team: TeamType) => state.scheduledByTeam[team] ?? [],
        upcomingByMonth: (state) => (team: TeamType) => {
            const items = [...(state.scheduledByTeam[team] ?? [])].sort(
                (a, b) => +new Date(a.kickoff_at) - +new Date(b.kickoff_at)
            )

            const keyOf = (iso: string) => {
                const d = new Date(iso)
                return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`
            }

            const titleOf = (iso: string) => {
                const d = new Date(iso)
                const fmt = new Intl.DateTimeFormat("sr-RS", {
                    month: "long",
                    year: "numeric",
                })
                const t = fmt.format(d)
                return t.charAt(0).toUpperCase() + t.slice(1)
            }

            const map = new Map<string, { title: string; items: Game[] }>()
            for (const m of items) {
                const key = keyOf(m.kickoff_at)
                if (!map.has(key))
                    map.set(key, {
                        title: titleOf(m.kickoff_at),
                        items: [],
                    })
                map.get(key)!.items.push(m)
            }

            return Array.from(map.entries())
                .sort((a, b) => a[0].localeCompare(b[0]))
                .map(([key, v]) => ({key, title: v.title, items: v.items}))
        },
        canLoadMoreResults: (state) => (team: TeamType) =>
            state.finishedPaginationByTeam[team].page < state.finishedPaginationByTeam[team].lastPage,

        lastResults: (state) => (team: TeamType) => state.finishedByTeam[team] ?? [],
    },

    actions: {
        // ✅ helper: dedup paralelnih istih requestova
        async _dedup<T>(key: string, fn: () => Promise<T>): Promise<T> {
            const existing = this._inFlight.get(key)
            if (existing) return existing as Promise<T>

            const p = fn().finally(() => this._inFlight.delete(key))
            this._inFlight.set(key, p)
            return p
        },

        // ✅ novi: minimalni load za homepage (last + next za svaki tim)
        async loadHomepageTabs() {
            await Promise.all(
                TEAMS.flatMap((t) => [
                    this.loadFinished(t, 1, 1, "replace"),
                    this.loadScheduled(t, 1),
                ])
            )
        },

        // ✅ eksplicitno: “staro ponašanje” (više rezultata, za stranice tipa fixtures)
        async loadFull() {
            await Promise.all([
                ...TEAMS.map((t) => this.loadScheduled(t, 200)),
                ...TEAMS.map((t) => this.loadFinished(t, 1, 3, "replace")),
            ])
        },

        // ✅ kompatibilno: ko već zove load(), ne ruši se
        // default sada radi "pametno" (homepage-friendly), a _force=true vuče full
        async load(_perPage = 50, _force = false) {
            if (_force) {
                await this.loadFull()
                return
            }
            await this.loadHomepageTabs()
        },

        async loadFinished(
            team: TeamType,
            page = 1,
            perPage = 3,
            mode: "replace" | "append" = "replace"
        ) {
            const key = `finished:${team}:${page}:${perPage}:${mode}`

            return this._dedup(key, async () => {
                try {
                    this.isLoadingFinishedByTeam[team] = true
                    this.error = null

                    const res = await fetchGames({
                        team_type: team,
                        status: "finished",
                        order: "desc",
                        page,
                        perPage,
                    })

                    if (mode === "replace") {
                        this.finishedByTeam[team] = res.items
                    } else {
                        const existing = new Set(this.finishedByTeam[team].map((x) => x.id))
                        const toAdd = res.items.filter((x) => !existing.has(x.id))
                        this.finishedByTeam[team] = [...this.finishedByTeam[team], ...toAdd]
                    }

                    this.finishedPaginationByTeam[team] = {
                        page: res.page,
                        perPage: res.perPage,
                        total: res.total,
                        lastPage: res.lastPage,
                    }
                } catch (e: any) {
                    this.error = e?.message ?? "Failed to load finished games"
                    if (mode === "replace") this.finishedByTeam[team] = []
                } finally {
                    this.isLoadingFinishedByTeam[team] = false
                }
            })
        },

        async loadMoreFinished(team: TeamType) {
            const pag = this.finishedPaginationByTeam[team]
            const next = pag.page + 1
            if (next > pag.lastPage) return
            await this.loadFinished(team, next, pag.perPage, "append")
        },

        async loadScheduled(team: TeamType, perPage = 200) {
            const key = `scheduled:${team}:${perPage}`

            return this._dedup(key, async () => {
                try {
                    this.isLoadingScheduledByTeam[team] = true
                    this.error = null

                    const res = await fetchGames({
                        team_type: team,
                        status: "scheduled",
                        order: "asc",
                        page: 1,
                        perPage,
                    })

                    this.scheduledByTeam[team] = res.items
                } catch (e: any) {
                    this.error = e?.message ?? "Failed to load scheduled games"
                    this.scheduledByTeam[team] = []
                } finally {
                    this.isLoadingScheduledByTeam[team] = false
                }
            })
        },
    },
})
