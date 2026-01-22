<template>
    <section class="w-full py-16 bg-[#071f36]">
        <h2 class="section-title-revers">LATEST RESULTS / NEXT MATCH</h2>

        <div class="container mx-auto px-4">
            <!-- Tabs (pill) -->
            <div class="flex justify-center mb-10">
                <div class="rounded-full bg-white/95 p-1 shadow-[0_14px_30px_rgba(0,0,0,0.22)]">
                    <div class="flex gap-1">
                        <button
                                v-for="t in tabs"
                                :key="t.key"
                                type="button"
                                @click="activeTab = t.key"
                                class="rounded-full px-7 py-3 text-xs sm:text-sm font-extrabold uppercase tracking-widest transition"
                                :class="activeTab === t.key
                ? 'bg-[#0A2D6B] text-white shadow-[0_10px_22px_rgba(0,0,0,0.18)]'
                : 'text-[#0A2D6B]/70 hover:text-[#0A2D6B]'"
                        >
                            {{ t.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards -->
            <div class="mx-auto max-w-6xl grid grid-cols-1 md:grid-cols-2 gap-7">
                <MatchCardLast
                        v-if="lastMatch"
                        :match="lastMatch"
                />
                <div
                        v-else
                        class="rounded-2xl bg-white/5 border border-white/10 p-6 text-white/70"
                >
                    Nema odigranih utakmica za ovu selekciju.
                </div>

                <MatchCardNext
                        v-if="nextMatch"
                        :match="nextMatch"
                />
                <div
                        v-else
                        class="rounded-2xl bg-white/5 border border-white/10 p-6 text-white/70"
                >
                    Nema zakazanih utakmica za ovu selekciju.
                </div>
            </div>

            <!-- Optional: error/info -->
            <p v-if="gamesStore.error"
               class="mt-6 text-red-300 text-sm text-center">
                {{ gamesStore.error }}
            </p>
        </div>
    </section>
</template>

<script setup lang="ts">
import {computed, ref} from "vue"
import MatchCardLast from "./MatchCardLast.vue"
import MatchCardNext from "./MatchCardNext.vue"
import type {LastMatch} from "./MatchCardLast.vue"
import type {NextMatch} from "./MatchCardNext.vue"

import {useGamesStore, type TeamType, type Game} from "@/stores/games"

type TabKey = "first" | "junior" | "women"

const tabs = [
    {
        key: "first" as const,
        label: "First Team",
        teamType: "first_team" as TeamType
    },
    {
        key: "junior" as const,
        label: "Junior Team",
        teamType: "youth" as TeamType
    },
    {key: "women" as const, label: "Women", teamType: "women" as TeamType},
]

const activeTab = ref<TabKey>("first")
const gamesStore = useGamesStore()

const API = import.meta.env.VITE_API_URL ?? "http://localhost:8080"

const activeTeamType = computed<TeamType>(() => {
    return tabs.find(t => t.key === activeTab.value)!.teamType
})

function logoUrl(logo: string | null) {
    return logo ? `${API}/storage/${logo}` : "/FK_Radnik_logo.png"
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString("sr-RS")
}

function formatTime(iso: string) {
    return new Date(iso).toLocaleTimeString("sr-RS", {
        hour: "2-digit",
        minute: "2-digit"
    })
}

function scoreText(g: Game) {
    // ako nisu unijeti golovi a status finished, da ne pukne UI
    const hs = g.home_score ?? 0
    const as = g.away_score ?? 0
    return `${hs} : ${as}`
}

const lastGame = computed(() => gamesStore.lastFinished(activeTeamType.value))
const nextGame = computed(() => gamesStore.nextUpcoming(activeTeamType.value))

const lastMatch = computed<LastMatch | null>(() => {
    const g = lastGame.value
    if (!g) return null

    return {
        home: {name: g.home_club.name, logo: logoUrl(g.home_club.logo)},
        away: {name: g.away_club.name, logo: logoUrl(g.away_club.logo)},
        score: scoreText(g),
        date: formatDate(g.kickoff_at),
        competition: g.round ?? "—",
    }
})

const nextMatch = computed<NextMatch | null>(() => {
    const g = nextGame.value
    if (!g) return null

    return {
        home: {name: g.home_club.name, logo: logoUrl(g.home_club.logo)},
        away: {name: g.away_club.name, logo: logoUrl(g.away_club.logo)},
        time: formatTime(g.kickoff_at),
        date: formatDate(g.kickoff_at),
        competition: g.round ?? "—",
    }
})
</script>
