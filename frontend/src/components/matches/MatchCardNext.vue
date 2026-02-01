<template>
    <div class="rounded-2xl bg-white shadow-[0_8px_25px_rgba(0,0,0,0.06)] overflow-hidden transition-all duration-300 hover:shadow-[0_15px_40px_rgba(10,45,107,0.15)] hover:-translate-y-1">
        <div class="px-6 py-5 bg-gradient-to-r from-gray-50/50 to-white">
            <div class="text-center">
                <div class="text-[14px] font-bold text-[#0A2D6B] uppercase tracking-wide mb-1">
                    {{ formattedRound }}
                </div>
                <div class="text-[13px] text-gray-500">
                    {{ formattedDate }}
                </div>
            </div>
        </div>

        <!-- Sadržaj utakmice -->
        <div class="px-6 py-6">
            <!-- Takmičenje i stadion -->
            <div class="text-center mb-6">
                <div class="text-[15px] font-bold text-gray-800 mb-1">
                    {{ match.competition || 'WWin Liga BiH' }}
                </div>
                <div v-if="match.stadium"
                     class="text-[13px] text-gray-600 flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor"
                         viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ match.stadium }}
                </div>
            </div>

            <!-- Timovi i vrijeme -->
            <div class="flex items-center justify-between mb-8">
                <!-- Domaćin -->
                <div class="text-center flex-1">
                    <div class="mb-4">
                        <img v-if="match.home.logo"
                             :src="match.home.logo"
                             :alt="match.home.name"
                             class="w-20 h-20 mx-auto object-contain">
                        <div v-else
                             class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <span class="text-gray-400 text-lg font-bold">{{
                                    match.home.short_name?.[0] || match.home.name?.[0]
                                }}</span>
                        </div>
                    </div>
                    <div class="text-[16px] font-bold text-gray-900">
                        {{ match.home.short_name || match.home.name }}
                    </div>
                    <div v-if="match.home.city"
                         class="text-[13px] text-gray-600 mt-1">
                        {{ match.home.city }}
                    </div>
                </div>

                <!-- Vrijeme -->
                <div class="mx-4 text-center">
                    <div class="text-[28px] font-black text-[#0A2D6B] mb-2">
                        {{ match.time }}
                    </div>
                    <div class="text-[13px] text-gray-500 font-medium">
                        Početak
                    </div>
                </div>

                <!-- Gost -->
                <div class="text-center flex-1">
                    <div class="mb-4">
                        <img v-if="match.away.logo"
                             :src="match.away.logo"
                             :alt="match.away.name"
                             class="w-20 h-20 mx-auto object-contain">
                        <div v-else
                             class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <span class="text-gray-400 text-lg font-bold">{{
                                    match.away.short_name?.[0] || match.away.name?.[0]
                                }}</span>
                        </div>
                    </div>
                    <div class="text-[16px] font-bold text-gray-900">
                        {{ match.away.short_name || match.away.name }}
                    </div>
                    <div v-if="match.away.city"
                         class="text-[13px] text-gray-600 mt-1">
                        {{ match.away.city }}
                    </div>
                </div>
            </div>

            <!-- Dugmadi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button
                        type="button"
                        @click="$emit('preview', match)"
                        class="w-full rounded-xl bg-gray-800 text-white py-3 text-sm font-bold hover:bg-gray-900 transition-colors flex items-center justify-center gap-2 group"
                >
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                         fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview →
                </button>
                <button
                        type="button"
                        @click="$emit('tickets', match)"
                        class="w-full rounded-xl bg-gradient-to-r from-[#0A2D6B] to-[#1e40af] text-white py-3 text-sm font-bold hover:from-[#1e40af] hover:to-[#0A2D6B] transition-all flex items-center justify-center gap-2 group shadow-md hover:shadow-lg"
                >
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                         fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    Tickets →
                </button>
            </div>
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

export type NextMatch = {
    home: Team
    away: Team
    time: string
    date: string
    competition: string
    round?: string | null
    stadium?: string | null
    status?: 'scheduled' | 'live' | 'finished'
    home_score?: number | null
    away_score?: number | null
    kickoff_at?: string
}

const props = defineProps<{ match: NextMatch }>()
const emit = defineEmits<{
    preview: [match: NextMatch]
    tickets: [match: NextMatch]
}>()

// Formatiraj rundu
const formattedRound = computed(() => {
    if (!props.match.round) return "UTAKMICA"

    const round = props.match.round.trim()
    // Ako je broj
    if (/^\d+$/.test(round)) {
        return `RUNDA ${round}`
    }
    // Ako već sadrži "runda" ili "kolo"
    if (round.toLowerCase().includes('runda') || round.toLowerCase().includes('kolo')) {
        return round.toUpperCase()
    }
    // Za kup utakmice
    if (round.toLowerCase().includes('finala')) {
        return round.toUpperCase()
    }
    if (round.toLowerCase().includes('ždrijeb')) {
        return 'ŽDRIJEB'
    }
    // Default
    return round.toUpperCase()
})

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