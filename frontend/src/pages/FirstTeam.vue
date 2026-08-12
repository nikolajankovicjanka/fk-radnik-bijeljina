<script setup lang="ts">
import {computed, onMounted, watch} from "vue"
import {i18n} from "@/i18n"

import {useNewsStore} from "@/stores/news"
import {useGamesStore} from "@/stores/games"
import type {TeamType} from "@/types/game"
import type {PlayerPosition} from "@/types/player"
import {usePlayersStore} from "@/stores/players"
import {useRevealOnScroll} from "@/composables/useRevealOnScroll"
import NewsCardHomepage from "@/components/news/NewsCardHomepage.vue"
import MatchCardNext from "@/components/matches/MatchCardNext.vue"
import PlayerCard from "@/components/players/PlayerCard.vue"
import {useStaffStore} from "@/stores/staffs"
import StaffCard from "@/components/staff/StaffCard.vue"

const TEAM: TeamType = "first_team"

const newsStore = useNewsStore()
const gamesStore = useGamesStore()
const playersStore = usePlayersStore()
const staffStore = useStaffStore()
const staff = computed(() => staffStore.activeByTeam(TEAM))

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

const toDateLocale = (loc: string) => {
    if (loc === "sr-Latn") return "sr-Latn-RS"
    if (loc === "sr-Cyrl") return "sr-Cyrl-RS"
    return loc
}

function formatDate(iso: string) {
    const d = new Date(iso)
    return d.toLocaleDateString(toDateLocale(i18n.global.locale.value), {
        day: "2-digit",
        month: "long",
        year: "numeric",
    })
}

function formatTime(iso: string) {
    const d = new Date(iso)
    return d.toLocaleTimeString(toDateLocale(i18n.global.locale.value), {
        hour: "2-digit",
        minute: "2-digit",
    })
}

const players = computed(() => playersStore.activeByTeam(TEAM))

type SectionKey = "GK" | "DEF" | "MID" | "ATT"

const SECTION_META: Record<
    SectionKey,
    {
        titleKey: string
        positions: PlayerPosition[]
    }
> = {
    GK: {
        titleKey: "pages.firstTeam.gk",
        positions: ["GK"],
    },
    DEF: {
        titleKey: "pages.firstTeam.def",
        positions: ["CB", "LB", "RB"],
    },
    MID: {
        titleKey: "pages.firstTeam.mid",
        positions: ["DM", "CM", "AM", "LM", "RM"],
    },
    ATT: {
        titleKey: "pages.firstTeam.att",
        positions: ["FC"],
    },
}

function posOrder(pos: PlayerPosition) {
    const order: PlayerPosition[] = [
        "GK",
        "CB",
        "LB",
        "RB",
        "DM",
        "CM",
        "AM",
        "LM",
        "RM",
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
        ATT: [],
    }

    for (const p of list) {
        if (SECTION_META.GK.positions.includes(p.position)) groups.GK.push(p)
        else if (SECTION_META.DEF.positions.includes(p.position)) groups.DEF.push(p)
        else if (SECTION_META.MID.positions.includes(p.position)) groups.MID.push(p)
        else if (SECTION_META.ATT.positions.includes(p.position)) groups.ATT.push(p)
    }

    return (Object.keys(SECTION_META) as SectionKey[]).map((key) => ({
        key,
        titleKey: SECTION_META[key].titleKey,
        players: groups[key],
    }))
})

const firstTeamNews = computed(() =>
    (newsStore.items ?? []).filter((n) => n.category === TEAM).slice(0, 6)
)

const nextMatch = computed(() => gamesStore.nextUpcoming(TEAM))

const nextMatchCard = computed(() => {
    const g = nextMatch.value
    if (!g) return null

    const roundText = g.round ? `${i18n.global.t("matches.round")} ${g.round}` : null
    const competitionText =
        g.competition ?? roundText ?? i18n.global.t("matches.defaultCompetition")

    return {
        home: {
            name: g.home_club?.name ?? i18n.global.t("matches.homeTeam"),
            logo: g.home_club?.logo ?? "/FK_Radnik_logo.png",
        },
        away: {
            name: g.away_club?.name ?? i18n.global.t("matches.awayTeam"),
            logo: g.away_club?.logo ?? "/FK_Radnik_logo.png",
        },
        time: formatTime(g.kickoff_at),
        date: formatDate(g.kickoff_at),
        competition: competitionText,
    }
})

watch(
    () => playersBySection.value.map((s) => s.players.length).join("|"),
    async () => {
        await refreshSections()
        await refreshCards()
    }
)
onMounted(async () => {
    newsStore.activeCategory = "first_team"

    const tasks: Promise<any>[] = []

    if (!(newsStore.items?.length)) tasks.push(newsStore.load(1))

    if (!gamesStore.upcomingAll(TEAM).length) {
        tasks.push(gamesStore.loadScheduled(TEAM, 200))
    }

    if (!playersStore.activeByTeam(TEAM).length) {
        tasks.push(
            playersStore.load({
                perPage: 200,
                team_type: TEAM,
                active: true,
            })
        )
    }

    if (!staffStore.activeByTeam(TEAM).length) {
        tasks.push(staffStore.load(TEAM))
    }

    await Promise.all(tasks)

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
                        src="/logo/First_team.webp"
                        :alt="$t('pages.firstTeam.heroAlt')"
                        class="h-full w-full object-cover"
                />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-black/10"/>


            <div class="absolute inset-x-0 bottom-0">
                <div class="mx-auto max-w-7xl px-4 pb-10 text-white">
                    <div class="flex items-end justify-between gap-6">
                        <div
                                class="rounded-3xl bg-black/40 backdrop-blur-md border border-white/15 shadow-xl px-5 py-4 sm:px-7 sm:py-6"
                        >
                            <div class="text-xs font-black tracking-[0.22em] uppercase text-white/90">
                                <h1>{{ $t("club.name") }}</h1>
                            </div>

                            <h2 class="mt-2 text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                                {{ $t("pages.firstTeam.title") }}
                            </h2>
                        </div>

                        <RouterLink
                                to="/fixtures"
                                class="hidden sm:inline-flex items-center justify-center rounded-xl bg-white/10 px-5 py-3 font-bold hover:bg-white/15 transition border border-white/15"
                        >
                            {{ $t("pages.firstTeam.allMatches") }} →
                        </RouterLink>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12">
            <div>
                <h2 class="n-title mb-4">
                    {{ $t("pages.firstTeam.tabela") }}
                    <span class="n-title-arrow">→</span>
                </h2>
            </div>
          <iframe id="sofa-standings-embed-2557-97761" src="https://widgets.sofascore.com/embed/tournament/2557/season/97761/standings?widgetTitle=WWIN+Liga+BiH&showCompetitionLogo=true&widgetTheme=light" frameborder="0" scrolling="no"
                  style="width:100%!important;
                  height:723px!important;max-width:1268px!important;"></iframe>

        </section>

        <!-- NEXT MATCH CARD -->
        <section class="mx-auto max-w-7xl px-4 py-10">
            <div>
                <h2 class="n-title mb-4">
                    {{ $t("pages.firstTeam.nextMatch") }}
                    <span class="n-title-arrow">→</span>
                </h2>
            </div>

            <div>
                <div
                        v-if="!nextMatchCard"
                        class="rounded-2xl border border-slate-100 p-8 text-slate-500 text-center"
                >
                    {{ $t("pages.firstTeam.noScheduled") }}
                </div>
                <MatchCardNext v-else :match="nextMatchCard"/>
            </div>
        </section>

        <!-- SQUAD -->
        <section class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="n-title">
                {{ $t("pages.firstTeam.squadTitle") }}
                <span class="n-title-arrow">→</span>
            </h2>

            <div class="mt-8 space-y-10">
                <section
                        v-for="sec in playersBySection"
                        :key="sec.key"
                        :ref="setSectionEl"
                        class="space-y-4 reveal-section"
                >
                    <div class="flex items-end justify-between gap-4">
                        <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#1650be]">
                            {{ $t(sec.titleKey) }}
                        </h3>
                    </div>

                    <div
                            v-if="sec.players.length === 0"
                            class="rounded-xl border border-slate-200 bg-slate-50 p-6 text-slate-500"
                    >
                        {{ $t("pages.firstTeam.noPlayersInGroup") }}
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

        <!-- STAFF -->
        <section class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="n-title">
                {{ $t("pages.firstTeam.staffTitle") ?? "Stručni štab" }}
                <span class="n-title-arrow">→</span>
            </h2>

            <div v-if="staff.length === 0"
                 class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-6 text-slate-500">
                Trenutno nema unosa stručnog štaba.
            </div>

            <div v-else
                 class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                <StaffCard v-for="m in staff" :key="m.id" :member="m"
                           :setCardEl="setCardEl"/>
            </div>
        </section>

        <!-- NEWS -->
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="n-title">
                            {{ $t("pages.firstTeam.newsTitle") }}
                            <span class="n-title-arrow">→</span>
                        </h2>
                    </div>

                    <RouterLink
                            :to="{ name: 'News', query: { category: 'all' } }"
                            class="hidden sm:inline-flex rounded-xl bg-[#071f36] px-5 py-3 text-white font-bold hover:brightness-110 transition"
                    >
                        {{ $t("pages.firstTeam.allNews") }} →
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

<style scoped></style>
