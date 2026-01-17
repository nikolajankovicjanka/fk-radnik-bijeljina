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
                <MatchCardLast :match="data[activeTab].last" />
                <MatchCardNext :match="data[activeTab].next" />
            </div>

        </div>
    </section>
</template>

<script setup lang="ts">
import { ref } from "vue"
import MatchCardLast from "./MatchCardLast.vue"
import MatchCardNext from "./MatchCardNext.vue"
import type { LastMatch } from "./MatchCardLast.vue"
import type { NextMatch } from "./MatchCardNext.vue"

type TabKey = "first" | "junior" | "women"

const tabs = [
    { key: "first" as const, label: "First Team" },
    { key: "junior" as const, label: "Junior Team" },
    { key: "women" as const, label: "Women" },
]

const activeTab = ref<TabKey>("first")

const data: Record<TabKey, { last: LastMatch; next: NextMatch }> = {
    first: {
        last: {
            home: { name: "FK Radnik", logo: "/FK_Radnik_logo.png" },
            away: { name: "FK Borac BL", logo: "/pl_logo/Borac_BL.png" },
            score: "2 : 0",
            date: "16.01.2026",
            competition: "First League RS",
        },
        next: {
            home: { name: "FK Radnik", logo: "/FK_Radnik_logo.png" },
            away: { name: "FK Sarajevo", logo: "/pl_logo/FK_Sarajevo.png" },
            time: "18:00",
            date: "22.01.2026",
            competition: "First League RS",
        },
    },
    junior: {
        last: {
            home: { name: "Radnik U21", logo: "/FK_Radnik_logo.png" },
            away: { name: "FK Kozara", logo: "/club/kozara-logo.png" },
            score: "3 : 1",
            date: "14.01.2026",
            competition: "Youth League",
        },
        next: {
            home: { name: "Radnik U21", logo: "/FK_Radnik_logo.png" },
            away: { name: "FK Sloboda", logo: "/club/sloboda-logo.png" },
            time: "12:00",
            date: "20.01.2026",
            competition: "Youth League",
        },
    },
    women: {
        last: {
            home: { name: "Radnik Women", logo: "/FK_Radnik_logo.png" },
            away: { name: "ŽFK Leotar", logo: "/club/leotar-logo.png" },
            score: "1 : 1",
            date: "15.01.2026",
            competition: "Women League",
        },
        next: {
            home: { name: "Radnik Women", logo: "/FK_Radnik_logo.png" },
            away: { name: "ŽFK Borac", logo: "/club/borac-logo.png" },
            time: "16:00",
            date: "23.01.2026",
            competition: "Women League",
        },
    },
}
</script>
