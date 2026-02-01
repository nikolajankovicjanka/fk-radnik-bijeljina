<template>
    <div class="rounded-2xl bg-white shadow-[0_8px_25px_rgba(0,0,0,0.06)] overflow-hidden mb-3 transition-all duration-300 hover:shadow-[0_15px_40px_rgba(10,45,107,0.15)] hover:-translate-y-1">
        <!-- Header sa rundom i datumom -->
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50/50 to-white">
            <div class="flex items-center justify-between">
                <div class="text-[11px] font-extrabold tracking-widest text-[#0A2D6B] uppercase">
                    {{ match.round ?? "UTAKMICA" }}
                </div>
                <div class="text-[11px] font-semibold text-gray-500">
                    {{ formattedDate }}
                </div>
            </div>
        </div>

        <!-- Sadržaj utakmice -->
        <div class="px-6 py-5">
            <div class="grid grid-cols-3 items-center gap-4">
                <!-- Domaćin -->
                <div class="text-center">
                    <div class="mb-2">
                        <img v-if="match.home.logo"
                             :src="match.home.logo"
                             :alt="match.home.name"
                             class="w-14 h-14 mx-auto object-contain">
                        <div v-else
                             class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <span class="text-gray-400 font-bold">{{
                                    match.home.short_name?.[0] || match.home.name?.[0]
                                }}</span>
                        </div>
                    </div>
                    <div class="text-sm font-bold text-gray-900 truncate px-1">
                        {{ match.home.short_name || match.home.name }}
                    </div>
                </div>

                <!-- Rezultat -->
                <div class="text-center">
                    <div
                            class="mx-auto inline-flex items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50/50 px-5 py-4 w-full max-w-[120px] hover:border-[#0A2D6B]/20 transition-colors"
                    >
                        <span class="text-3xl font-black text-[#0A2D6B] leading-none">
                            {{ match.score }}
                        </span>
                    </div>

                    <div class="mt-2 text-[12px] text-gray-600 font-semibold">
                        {{ match.competition }}
                    </div>

                    <!-- Dodatni status ako je završeno -->
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                            ZAVRŠENO
                        </span>
                    </div>
                </div>

                <!-- Gost -->
                <div class="text-center">
                    <div class="mb-2">
                        <img v-if="match.away.logo"
                             :src="match.away.logo"
                             :alt="match.away.name"
                             class="w-14 h-14 mx-auto object-contain">
                        <div v-else
                             class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <span class="text-gray-400 font-bold">{{
                                    match.away.short_name?.[0] || match.away.name?.[0]
                                }}</span>
                        </div>
                    </div>
                    <div class="text-sm font-bold text-gray-900 truncate px-1">
                        {{ match.away.short_name || match.away.name }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Dugme -->
        <div class="px-6 pb-6">
            <button
                    type="button"
                    @click="$emit('details', match)"
                    class="w-full rounded-xl bg-gradient-to-r from-[#0A2D6B] to-[#1e40af] text-white py-3 text-sm font-bold hover:from-[#1e40af] hover:to-[#0A2D6B] transition-all shadow-md hover:shadow-lg group"
            >
                <span class="flex items-center justify-center gap-2">
                    Match Centre
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed} from 'vue'

export type Team = {
    id: number
    name: string
    short_name?: string
    logo?: string
    city?: string
}

export type LastMatch = {
    home: Team
    away: Team
    score: string
    date: string
    competition: string
    round?: string | null
    kickoff_at?: string
}

const props = defineProps<{ match: LastMatch }>()
const emit = defineEmits<{
    details: [match: LastMatch]
}>()

// Formatiraj datum (14. фебруар 2026.)
const formattedDate = computed(() => {
    if (!props.match.kickoff_at) return props.match.date

    try {
        const date = new Date(props.match.kickoff_at)

        // Mjeseci na srpskom/bošnjačkom
        const months = [
            'јануар', 'фебруар', 'март', 'април', 'мај', 'јун',
            'јул', 'август', 'септембар', 'октобар', 'новембар', 'децембар'
        ]

        const day = date.getDate()
        const month = months[date.getMonth()]
        const year = date.getFullYear()

        return `${day}. ${month} ${year}.`
    } catch {
        return props.match.date
    }
})
</script>