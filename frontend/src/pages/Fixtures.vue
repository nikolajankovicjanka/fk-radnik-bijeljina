<script setup lang="ts">
import {computed} from "vue"
import {useGamesStore} from "@/stores/games"
import type {TeamType, Game} from "@/types/game"

import MatchCardLast from "@/components/matches/MatchCardLast.vue"
import MatchCardNext from "@/components/matches/MatchCardNext.vue"

import type {LastMatch} from "@/components/matches/MatchCardLast.vue"
import type {NextMatch} from "@/components/matches/MatchCardNext.vue"

const TEAM: TeamType = "first_team"
const gamesStore = useGamesStore()

function formatDateLong(iso: string) {
    const d = new Date(iso)
    return d.toLocaleDateString("sr-RS", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    })
}

function formatDateShort(iso: string) {
    return new Date(iso).toLocaleDateString("sr-RS")
}

function formatTime(iso: string) {
    return new Date(iso).toLocaleTimeString("sr-RS", {
        hour: "2-digit",
        minute: "2-digit",
    })
}

function scoreText(g: Game) {
    const hs = g.home_score ?? 0
    const as = g.away_score ?? 0
    return `${hs} : ${as}`
}

// Ako želiš da competition bude takmičenje, a round da ide gore u headeru kartice:
function competitionText(g: Game) {
    return g.competition ?? "—"
}

function safeLogo(logo: string | null | undefined) {
    return logo ?? "/FK_Radnik_logo.png"
}

const lastResults = computed(() => gamesStore.lastResults(TEAM))

const lastResultCards = computed<LastMatch[]>(() =>
    lastResults.value.slice(0, 3).map((g) => ({
        home: {name: g.home_club.name, logo: safeLogo(g.home_club.logo)},
        away: {name: g.away_club.name, logo: safeLogo(g.away_club.logo)},
        score: scoreText(g),
        date: formatDateShort(g.kickoff_at),
        competition: competitionText(g),
        round: g.round ?? "—",
    }))
)

const upcomingGroups = computed(() => gamesStore.upcomingByMonth(TEAM))

const upcomingCardsByMonth = computed(() =>
    upcomingGroups.value.map((grp) => ({
        key: grp.key,
        title: grp.title,
        items: grp.items.map(
            (g): NextMatch => ({
                home: {
                    name: g.home_club.name,
                    logo: safeLogo(g.home_club.logo)
                },
                away: {
                    name: g.away_club.name,
                    logo: safeLogo(g.away_club.logo)
                },
                time: formatTime(g.kickoff_at),
                date: formatDateLong(g.kickoff_at),
                competition: competitionText(g),
                round: g.round ?? "—",
                stadium: g.stadium ?? null,
            })
        ),
    }))
)
</script>

<template>
    <main class="bg-white">
        <section class="relative overflow-hidden bg-[#071f36]">
            <div class="absolute inset-0">
                <div class="h-full w-full bg-gradient-to-b from-[#0A2D6B] via-[#071f36] to-[#071f36]"/>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:py-20">
                <div class="max-w-3xl">
                    <h1 class="text-white text-4xl sm:text-6xl font-extrabold tracking-tight">
                        FK Radnik Soccerbet
                    </h1>
                    <p class="mt-5 text-white/85 text-base sm:text-lg leading-relaxed">
                        Pregled odigranih i narednih utakmica
                        Bijeljinskog Radnika
                    </p>
                </div>
            </div>
        </section>

        <!-- LAST RESULTS -->
        <section class="mx-auto max-w-7xl px-4 py-12">
            <div class="flex items-end justify-between gap-4">
                <h2 class="n-title">
                    Posljednji rezultati <span class="n-title-arrow">→</span>
                </h2>

                <button
                        v-if="gamesStore.canLoadMoreResults(TEAM)"
                        type="button"
                        class="hidden sm:inline-flex rounded-xl bg-[#071f36] px-5 py-3 text-white font-bold hover:brightness-110 transition"
                        :disabled="gamesStore.isLoadingFinishedByTeam?.[TEAM]"
                        @click="gamesStore.loadMoreFinished(TEAM)"
                >
                    Učitaj još →
                </button>
            </div>

            <div class="mt-8 space-y-6">
                <template v-if="lastResultCards.length">
                    <MatchCardLast
                            v-for="(m, i) in lastResultCards"
                            :key="i"
                            :match="m"
                    />
                </template>

                <div
                        v-else
                        class="rounded-2xl border border-slate-200 bg-slate-50 p-8 text-slate-500 text-center"
                >
                    Nema rezultata.
                </div>
            </div>

            <!-- mobile load more -->
            <div class="mt-8 sm:hidden">
                <button
                        v-if="gamesStore.canLoadMoreResults(TEAM)"
                        type="button"
                        class="w-full rounded-xl bg-[#071f36] py-3 text-white font-bold hover:brightness-110 transition"
                        :disabled="gamesStore.isLoadingFinishedByTeam?.[TEAM]"
                        @click="gamesStore.loadMoreFinished(TEAM)"
                >
                    Učitaj još →
                </button>
            </div>
        </section>

        <!-- UPCOMING -->
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <section v-for="grp in upcomingCardsByMonth" :key="grp.key"
                         class="space-y-6">
                    <h3 class="text-2xl mt-5 font-extrabold tracking-tight text-[#1650be]">
                        {{ grp.title }}
                    </h3>

                    <div class="space-y-6">
                        <MatchCardNext
                                v-for="(m, i) in grp.items"
                                :key="`${grp.key}-${i}`"
                                :match="m"
                        />
                    </div>
                </section>
            </div>
        </section>
    </main>
</template>
