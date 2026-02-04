<script setup lang="ts">
import {computed, ref} from "vue"
import MatchCardLast from "./MatchCardLast.vue"
import MatchCardNext from "./MatchCardNext.vue"
import type {LastMatch} from "./MatchCardLast.vue"
import type {NextMatch} from "./MatchCardNext.vue"

import {useGamesStore, type TeamType, type Game} from "@/stores/games"
import {i18n} from "@/i18n"

type TabKey = "first" | "junior" | "women"

const tabs = [
    {
        key: "first" as const,
        labelKey: "common.firstTeam",
        teamType: "first_team" as TeamType
    },
    {
        key: "junior" as const,
        labelKey: "common.youth",
        teamType: "youth" as TeamType
    },
    {
        key: "women" as const,
        labelKey: "common.women",
        teamType: "women" as TeamType
    },
] as const

const activeTab = ref<TabKey>("first")
const gamesStore = useGamesStore()

const activeTeamType = computed<TeamType>(
    () => tabs.find(t => t.key === activeTab.value)!.teamType
)

const toDateLocale = (loc: string) => {
    if (loc === "sr-Latn") return "sr-Latn-RS"
    if (loc === "sr-Cyrl") return "sr-Cyrl-RS"
    return loc
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString(toDateLocale(i18n.global.locale.value))
}

function formatTime(iso: string) {
    return new Date(iso).toLocaleTimeString(toDateLocale(i18n.global.locale.value), {
        hour: "2-digit",
        minute: "2-digit",
    })
}

function scoreText(g: Game) {
    const hs = g.home_score ?? 0
    const as = g.away_score ?? 0
    return `${hs} : ${as}`
}

function safeLogo(logo: string | null | undefined) {
    return logo ?? "/FK_Radnik_logo.png"
}

const lastGame = computed(() => gamesStore.lastFinished(activeTeamType.value))
const nextGame = computed(() => gamesStore.nextUpcoming(activeTeamType.value))

const lastMatch = computed<LastMatch | null>(() => {
    const g = lastGame.value
    if (!g) return null

    return {
        home: {name: g.home_club.name, logo: safeLogo(g.home_club.logo)},
        away: {name: g.away_club.name, logo: safeLogo(g.away_club.logo)},
        score: scoreText(g),
        date: formatDate(g.kickoff_at),
        competition: g.competition ?? "—",
        round: g.round ?? "—",
    }
})

const nextMatch = computed<NextMatch | null>(() => {
    const g = nextGame.value
    if (!g) return null

    return {
        home: {name: g.home_club.name, logo: safeLogo(g.home_club.logo)},
        away: {name: g.away_club.name, logo: safeLogo(g.away_club.logo)},
        time: formatTime(g.kickoff_at),
        date: formatDate(g.kickoff_at),
        competition: g.competition ?? "—",
        round: g.round ?? "—",
        stadium: g.stadium ?? null,
    }
})
</script>
<template>
    <section class="w-full py-16 bg-[#071f36]">
        <h2 class="section-title-revers mb-3">
            {{ $t('home.matchesTabs.title') }}
        </h2>

        <div class="container mx-auto px-4">
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
                            {{ $t(t.labelKey) }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards -->
            <div class="mx-auto max-w-6xl grid grid-cols-1 md:grid-cols-2 gap-7 items-stretch">
                <div class="h-full">
                    <MatchCardLast v-if="lastMatch" :match="lastMatch"
                                   class="h-full"/>
                    <div
                            v-else
                            class="h-full rounded-2xl bg-white/5 border border-white/10 p-6 text-white/70"
                    >
                        {{ $t('home.matchesTabs.emptyLast') }}
                    </div>
                </div>

                <div class="h-full">
                    <MatchCardNext v-if="nextMatch" :match="nextMatch"
                                   class="h-full"/>
                    <div
                            v-else
                            class="h-full rounded-2xl bg-white/5 border border-white/10 p-6 text-white/70"
                    >
                        {{ $t('home.matchesTabs.emptyNext') }}
                    </div>
                </div>
            </div>

            <p v-if="gamesStore.error"
               class="mt-6 text-red-300 text-sm text-center">
                {{ gamesStore.error }}
            </p>
        </div>
    </section>
</template>