<script setup lang="ts">
import {computed, onMounted} from "vue"
import {useNewsStore} from "@/stores/news"
import {useGamesStore, type TeamType, type Game} from "@/stores/games"
import {usePlayersStore} from "@/stores/players"

const TEAM: TeamType = "first_team"

import NewsCardHomepage from "@/components/news/NewsCardHomepage.vue"
import MatchCardNext from "@/components/matches/MatchCardNext.vue"
import {storeToRefs} from "pinia";


const newsStore = useNewsStore()
const gamesStore = useGamesStore()
const playersStore = usePlayersStore()


function formatDate(iso: string) {
    const d = new Date(iso)
    return d.toLocaleDateString("sr-RS", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    })
}

const players = computed(() => playersStore.activeByTeam(TEAM))

function formatTime(iso: string) {
    const d = new Date(iso)
    return d.toLocaleTimeString("sr-RS", {hour: "2-digit", minute: "2-digit"})
}

function clubLogoUrl(path?: string | null) {
    const API = import.meta.env.VITE_API_URL ?? "http://localhost:8080"
    if (!path) return "/FK_Radnik_logo.png"
    // backend ti vraća: "clubs/xxx.png"
    return `${API}/storage/${path}`
}

function getFirstName(fullName: string) {
    const parts = fullName.split(' ')
    return parts[0] || fullName
}

function getLastName(fullName: string) {
    const parts = fullName.split(' ')
    return parts.slice(1).join(' ') || ''
}

const firstTeamNews = computed(() =>
    (newsStore.items ?? []).filter((n) => n.category === TEAM).slice(0, 6)
)

const nextMatch = computed(() => gamesStore.nextUpcoming(TEAM))

const nextMatchCard = computed(() => {
    const g = nextMatch.value
    if (!g) return null

    return {
        home: {
            name: g.home_club?.name ?? "Domaćin",
            logo: clubLogoUrl(g.home_club?.logo)
        },
        away: {
            name: g.away_club?.name ?? "Gost",
            logo: clubLogoUrl(g.away_club?.logo)
        },
        time: formatTime(g.kickoff_at),
        date: formatDate(g.kickoff_at),
        competition: g.round ? `Kolo ${g.round}` : "WWin Liga Premier Liga",
    }
})

const upcomingFive = computed<Game[]>(() => {
    const now = new Date()
    return (gamesStore.items ?? [])
        .filter((g) => g.team_type === TEAM && new Date(g.kickoff_at) >= now)
        .sort((a, b) => +new Date(a.kickoff_at) - +new Date(b.kickoff_at))
        .slice(0, 5)
})

onMounted(async () => {
    newsStore.activeCategory = 'first_team'
    if (!newsStore.items.length) await newsStore.load(1)
    if (!gamesStore.items.length) await gamesStore.load(50)
    if (!playersStore.items.length) await playersStore.load(200)
})
</script>

<template>
    <main class="bg-white">
        <!-- HERO -->
        <section class="relative overflow-hidden">
            <div class="h-[320px] sm:h-[420px] w-full">
                <img
                        src="/logo/First_team.JPG"
                        alt="Prvi tim"
                        class="h-full w-full object-cover"
                />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-black/10"/>

            <div class="absolute inset-x-0 bottom-0">
                <div class="mx-auto max-w-7xl px-4 pb-10 text-white">
                    <div class="flex items-end justify-between gap-6">
                        <div>
                            <div class="text-xs font-black tracking-[0.22em] uppercase opacity-90">
                                FK RADNIK BIJELJINA
                            </div>
                            <h1 class="mt-2 text-3xl sm:text-5xl font-extrabold tracking-tight">
                                Prvi tim
                            </h1>
                            <p class="mt-3 max-w-2xl text-white/90">
                                Roster, vijesti i raspored utakmica — sve na
                                jednom mjestu.
                            </p>
                        </div>

                        <RouterLink
                                to="/fixtures"
                                class="hidden sm:inline-flex items-center justify-center rounded-xl bg-white/10 px-5 py-3
                     font-bold hover:bg-white/15 transition border border-white/15"
                        >
                            Svi mečevi →
                        </RouterLink>
                    </div>
                </div>
            </div>
        </section>


        <section class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="n-title">PRVI TIM <span class="n-title-arrow">→</span>
            </h2>
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                <article
                        v-for="p in players"
                        :key="p.id"
                        class="group relative overflow-hidden rounded-2xl border border-gray-200 shadow-lg"
                >
                    <!-- SLIKA -->
                    <img
                            :src="p.photo ?? '/players/placeholder.png'"
                            :alt="p.name"
                            class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />

                    <!-- OVERLAY -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                    <!-- BROJ -->
                    <div class="absolute top-3 left-3">
                        <div class="text-3xl font-black text-white/90">
                            #{{ p.shirt_number ?? "—" }}
                        </div>
                    </div>

                    <!-- IME -->
                    <div class="absolute bottom-3 left-3 right-3">
                        <div class="text-lg font-black text-white truncate">
                            {{ p.name }}
                        </div>
                        <div class="text-sm font-bold text-white/80 uppercase tracking-wide">
                            {{ p.position ?? "Player" }}
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- NEWS (samo first_team) -->
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="n-title">PRVI TIM - vijesti <span
                                class="n-title-arrow">→</span>
                        </h2>
                    </div>

                    <RouterLink
                            :to="{ name: 'News', query: { category: 'all' } }"
                            class="hidden sm:inline-flex rounded-xl bg-[#071f36] px-5 py-3 text-white font-bold hover:brightness-110 transition"
                    >
                        Sve vijesti →
                    </RouterLink>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <NewsCardHomepage v-for="n in firstTeamNews" :key="n.id"
                                      :item="n"/>
                </div>
            </div>
        </section>

        <!-- NEXT MATCH CARD -->
        <section class="mx-auto max-w-7xl px-4 py-12">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-[#071f36] mb-2">
                    Naredna utakmica
                </h2>
            </div>

            <div>
                <div v-if="!nextMatchCard"
                     class="rounded-2xl border border-slate-100 p-8 text-slate-500 text-center">
                    Trenutno nema zakazanih utakmica.
                </div>
                <MatchCardNext v-else :match="nextMatchCard"/>
            </div>
        </section>

        <!-- UPCOMING MATCHES - FULL WIDTH -->
        <section class="bg-slate-50 py-12">
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex items-end justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#071f36]">
                            Naredni mečevi
                        </h2>
                        <p class="mt-2 text-slate-500">Kalendar utakmica za prvi
                            tim</p>
                    </div>
                    <RouterLink to="/fixtures"
                                class="hidden sm:inline-flex items-center gap-2 text-sm font-bold text-[#0A2D6B] hover:underline">
                        Pogledaj kompletan raspored
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </RouterLink>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <article
                            v-for="g in upcomingFive"
                            :key="g.id"
                            class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden"
                    >
                        <!-- DATE HEADER -->
                        <div class="bg-[#071f36] text-white px-4 py-2">
                            <div class="text-sm font-bold">
                                {{ formatDate(g.kickoff_at) }}
                            </div>
                            <div class="text-xs opacity-90">
                                {{ formatTime(g.kickoff_at) }}
                            </div>
                        </div>

                        <!-- MATCH CONTENT -->
                        <div class="p-4">
                            <!-- TEAMS -->
                            <div class="space-y-4">
                                <!-- HOME TEAM -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img
                                                :src="clubLogoUrl(g.home_club?.logo)"
                                                class="h-8 w-8 object-contain"
                                                alt=""
                                        />
                                        <div class="font-bold text-gray-900 truncate">
                                            {{ g.home_club?.name }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500">
                                        Domaćin
                                    </div>
                                </div>

                                <!-- VS SEPARATOR -->
                                <div class="flex items-center justify-center">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        vs
                                    </div>
                                </div>

                                <!-- AWAY TEAM -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img
                                                :src="clubLogoUrl(g.away_club?.logo)"
                                                class="h-8 w-8 object-contain"
                                                alt=""
                                        />
                                        <div class="font-bold text-gray-900 truncate">
                                            {{ g.away_club?.name }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500">
                                        Gost
                                    </div>
                                </div>
                            </div>

                            <!-- MATCH INFO -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-between text-sm">
                                    <div class="text-gray-600">
                                        <span class="font-bold">Stadion:</span>
                                        <span class="ml-1">{{
                                                g.stadium || 'TBD'
                                            }}</span>
                                    </div>
                                    <div v-if="g.round"
                                         class="font-bold text-[#0A2D6B]">
                                        Kolo {{ g.round }}
                                    </div>
                                </div>
                            </div>

                            <!-- ACTIONS -->
                            <div class="mt-4 flex gap-2">
                                <RouterLink
                                        :to="`/match/${g.id}`"
                                        class="flex-1 text-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 text-sm transition"
                                >
                                    Detalji
                                </RouterLink>
                                <button
                                        type="button"
                                        class="flex-1 text-center rounded-lg bg-[#0A2D6B] hover:bg-[#0A2D6B]/90 text-white font-bold py-2 text-sm transition"
                                >
                                    Ulaznice
                                </button>
                            </div>
                        </div>
                    </article>

                    <!-- EMPTY STATE -->
                    <div v-if="upcomingFive.length === 0"
                         class="col-span-full text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="1.5"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">Trenutno nema narednih
                            utakmica.</p>
                    </div>
                </div>

                <!-- MOBILE VIEW ALL LINK -->
                <div class="mt-8 text-center sm:hidden">
                    <RouterLink to="/fixtures"
                                class="inline-flex items-center gap-2 text-sm font-bold text-[#0A2D6B] hover:underline">
                        Svi mečevi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </RouterLink>
                </div>
            </div>
        </section>
    </main>
</template>