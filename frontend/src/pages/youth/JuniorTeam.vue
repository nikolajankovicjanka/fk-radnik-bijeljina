<script setup lang="ts">
import {computed, onMounted, watch} from "vue"
import {i18n} from "@/i18n"

import type {TeamType} from "@/types/game"
import type {PlayerPosition} from "@/types/player"

import {usePlayersStore} from "@/stores/players"
import {useGamesStore} from "@/stores/games"
import {useStaffStore} from "@/stores/staffs"

import {useRevealOnScroll} from "@/composables/useRevealOnScroll"
import {inYouthGroup} from "@/utils/youthGroups"

import PlayerCard from "@/components/players/PlayerCard.vue"
import MatchCardNext from "@/components/matches/MatchCardNext.vue"
import StaffCard from "@/components/staff/StaffCard.vue"

const TEAM: TeamType = "youth"
const GROUP = "u19" as const

const playersStore = usePlayersStore()
const gamesStore = useGamesStore()
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

const players = computed(() =>
    playersStore
        .activeByTeam(TEAM)
        .filter((p) => inYouthGroup(p.birth_year, GROUP))
)

type SectionKey = "GK" | "DEF" | "MID" | "ATT"

const SECTION_META: Record<
    SectionKey,
    {
        titleKey: string
        positions: PlayerPosition[]
    }
> = {
    GK: {titleKey: "pages.firstTeam.gk", positions: ["GK"]},
    DEF: {titleKey: "pages.firstTeam.def", positions: ["CB", "LB", "RB"]},
    MID: {
        titleKey: "pages.firstTeam.mid",
        positions: ["DM", "CM", "AM", "LM", "RM"],
    },
    ATT: {titleKey: "pages.firstTeam.att", positions: ["FC"]},
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
    // players (youth) - učitaj ako nije učitano (bitno: items može biti undefined)
    if (!(playersStore.items?.length)) {
        await playersStore.load({
            perPage: 400,
            team_type: TEAM,
            active: true,
        })
    }

    if (!(gamesStore.items?.length)) await gamesStore.load(50)

    await refreshSections()
    await refreshCards()

    // staff (youth)
    if (!staffStore.activeByTeam(TEAM).length) await staffStore.load(TEAM)
})
</script>

<template>
    <main class="bg-white">
        

        <!-- NEXT MATCH CARD -->
        <section class="mx-auto max-w-7xl px-4 py-12">
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
                        Nema unosa u ovoj grupi.
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

            <div
                    v-if="staff.length === 0"
                    class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-6 text-slate-500"
            >
                Trenutno nema unosa stručnog štaba.
            </div>

            <div v-else
                 class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                <StaffCard v-for="m in staff" :key="m.id" :member="m"
                           :setCardEl="setCardEl"/>
            </div>
        </section>
    </main>
</template>

<style scoped></style>