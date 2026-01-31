<script setup lang="ts">
import {computed, onMounted, watch} from "vue"
import {useNewsStore} from "@/stores/news"
import {useGamesStore, type TeamType, type Game} from "@/stores/games"
import {usePlayersStore} from "@/stores/players"
import {useRevealOnScroll} from "@/composables/useRevealOnScroll"

const TEAM: TeamType = "first_team"

import NewsCardHomepage from "@/components/news/NewsCardHomepage.vue"
import MatchCardNext from "@/components/matches/MatchCardNext.vue"
import PlayerCard from "@/components/players/PlayerCard.vue"


const newsStore = useNewsStore()
const gamesStore = useGamesStore()
const playersStore = usePlayersStore()

const {setRef: setSectionEl, refresh: refreshSections} = useRevealOnScroll({
    rootMargin: "0px 0px -15% 0px",
    threshold: 0.12,
    once: true,
    visibleClass: "is-visible",
})

const {setRef: setCardEl, refresh: refreshCards} = useRevealOnScroll({
    rootMargin: "0px 0px -10% 0px",
    threshold: 0.12,
    once: true,
    visibleClass: "is-visible",
})


function formatDate(iso: string) {
    const d = new Date(iso)
    return d.toLocaleDateString("sr-RS", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    })
}

const players = computed(() => playersStore.activeByTeam(TEAM))

type SectionKey = "GK" | "DEF" | "MID" | "ATT"

const SECTION_META: Record<SectionKey, {
    title: string;
    positions: PlayerPosition[]
}> = {
    GK: {
        title: "GOLMANI",
        positions: ["GK"],
    },
    DEF: {
        title: "ODBRANA",
        positions: ["CB", "LB", "RB"],
    },
    MID: {
        title: "VEZNI RED",
        positions: ["DM", "CM", "AM", "LM", "RM"],
    },
    ATT: {
        title: "NAPADAČI",
        positions: ["FC"],
    },
}

function posOrder(pos: PlayerPosition) {
    const order: PlayerPosition[] = [
        "GK",
        "CB", "LB", "RB",
        "DM", "CM", "AM", "LM", "RM",
        "FC",
    ]
    return order.indexOf(pos)
}

const playersBySection = computed(() => {
    const list = (players.value ?? []).slice()

    list.sort((a, b) => {
        const po = posOrder(a.position) - posOrder(b.position)
        if (po !== 0) return po
        return (a.shirt_number ?? 999) - (b.shirt_number ?? 999)
    })

    const groups: Record<SectionKey, typeof list> = {
        GK: [],
        DEF: [],
        MID: [],
        ATT: []
    }

    for (const p of list) {
        if (SECTION_META.GK.positions.includes(p.position)) groups.GK.push(p)
        else if (SECTION_META.DEF.positions.includes(p.position)) groups.DEF.push(p)
        else if (SECTION_META.MID.positions.includes(p.position)) groups.MID.push(p)
        else if (SECTION_META.ATT.positions.includes(p.position)) groups.ATT.push(p)
    }

    return (Object.keys(SECTION_META) as SectionKey[]).map((key) => ({
        key,
        title: SECTION_META[key].title,
        players: groups[key],
    }))
})

function formatTime(iso: string) {
    const d = new Date(iso)
    return d.toLocaleTimeString("sr-RS", {hour: "2-digit", minute: "2-digit"})
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
            logo: g.home_club?.logo ?? "/FK_Radnik_logo.png",
        },
        away: {
            name: g.away_club?.name ?? "Gost",
            logo: g.away_club?.logo ?? "/FK_Radnik_logo.png",
        },
        time: formatTime(g.kickoff_at),
        date: formatDate(g.kickoff_at),
        competition: g.competition ?? (g.round ? `Kolo ${g.round}` : "WWin Liga Premier Liga"),
    }
})

const upcomingFive = computed<Game[]>(() => {
    const now = new Date()
    return (gamesStore.items ?? [])
        .filter((g) => g.team_type === TEAM && new Date(g.kickoff_at) >= now)
        .sort((a, b) => +new Date(a.kickoff_at) - +new Date(b.kickoff_at))
        .slice(0, 5)
})

watch(
    () => playersBySection.value.map(s => s.players.length).join("|"),
    async () => {
        await refreshSections()
        await refreshCards()
    }
)

onMounted(async () => {
    newsStore.activeCategory = 'first_team'
    if (!newsStore.items.length) await newsStore.load(1)
    if (!gamesStore.items.length) await gamesStore.load(50)
    if (!playersStore.items.length) await playersStore.load(200)

    await refreshSections()
    await refreshCards()
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
                        <div class="rounded-3xl bg-black/40 backdrop-blur-md  border border-white/15 shadow-xl  px-5 py-4 sm:px-7 sm:py-6">
                            <div class="text-xs font-black tracking-[0.22em] uppercase text-white/90">
                                FK RADNIK BIJELJINA
                            </div>

                            <h1 class="mt-2 text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                                Prvi tim
                            </h1>
                        </div>

                        <RouterLink
                                to="/fixtures"
                                class="hidden sm:inline-flex items-center justify-center rounded-xl bg-white/10 px-5 py-3
                                        font-bold hover:bg-white/15 transition border border-white/15">
                            Svi mečevi →
                        </RouterLink>
                    </div>
                </div>
            </div>
        </section>

        <!-- NEXT MATCH CARD -->
        <section class="mx-auto max-w-7xl px-4 py-12">
            <div>
                <h2 class="n-title mb-4"> Naredna utakmica <span
                        class="n-title-arrow">→</span>
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


        <section class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="n-title">PRVI TIM <span class="n-title-arrow">→</span>
            </h2>
            <div class="mt-8 space-y-10">
                <section
                        v-for="sec in playersBySection"
                        :key="sec.key"
                        :ref="setSectionEl"
                        class="space-y-4 reveal-section">
                    <div class="flex items-end justify-between gap-4">
                        <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#1650be]">
                            {{ sec.title }}
                        </h3>
                    </div>

                    <div v-if="sec.players.length === 0"
                         class="rounded-xl border border-slate-200 bg-slate-50 p-6 text-slate-500">
                        Nema igrača u ovoj grupi.
                    </div>

                    <div v-else
                         class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                        <PlayerCard
                                v-for="p in sec.players"
                                :key="p.id"
                                :player="p"
                                :setCardEl="setCardEl"
                        />
                    </div>
                </section>
            </div>
        </section>

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
    </main>
</template>

<style scoped>
</style>
