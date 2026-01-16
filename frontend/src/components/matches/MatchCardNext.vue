<template>
    <div class="rounded-2xl bg-white shadow-[0_18px_40px_rgba(0,0,0,0.18)] overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="text-[11px] font-extrabold tracking-widest text-slate-500 uppercase">
                NEXT MATCH
            </div>
            <div class="text-[11px] font-semibold text-slate-400">
                {{ match.date }}
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="flex items-center justify-between gap-4">
                <TeamBlock :team="match.home" />

                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-6 py-3">
            <span class="text-xl font-extrabold tracking-tight text-[#0A2D6B]">
              {{ match.time }}
            </span>
                    </div>
                    <div class="mt-2 text-[12px] text-slate-500 font-semibold">
                        {{ match.competition }}
                    </div>
                </div>

                <TeamBlock :team="match.away" />
            </div>
        </div>

        <div class="px-6 pb-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button
                    type="button"
                    class="w-full rounded-xl bg-[#0A2D6B] py-3 text-white font-bold text-sm hover:brightness-105 transition"
            >
                Preview →
            </button>
            <button
                    type="button"
                    class="w-full rounded-xl bg-[#0B39FF] py-3 text-white font-bold text-sm hover:brightness-105 transition"
            >
                Tickets →
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import TeamBlock, { type Team } from "./TeamBlock.vue"

export type NextMatch = {
    home: Team
    away: Team
    time: string
    date: string
    competition: string
}

defineProps<{ match: NextMatch }>()
</script>
