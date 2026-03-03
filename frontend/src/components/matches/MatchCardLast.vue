<template>
    <div
            class="h-full flex flex-col rounded-2xl bg-white shadow-[0_8px_25px_rgba(0,0,0,0.06)] overflow-hidden mb-3 transition-all duration-300 hover:shadow-[0_15px_40px_rgba(10,45,107,0.15)] hover:-translate-y-1"
    >
        <!-- Header sa rundom i datumom -->
        <div class="px-5 py-3 bg-gradient-to-r from-blue-50/40 to-white">
            <div class="flex items-center justify-between">
                <div class="text-[11px] font-extrabold tracking-widest text-[#0A2D6B] uppercase">
                    {{ match.round ?? $t("matches.match") }}
                </div>
                <div class="text-[11px] font-semibold text-gray-500">
                    {{ formattedDate }}
                </div>
            </div>
        </div>

        <!-- Sadržaj utakmice -->
        <div class="px-6 py-5 flex-1">
            <div class="grid grid-cols-3 items-center gap-3">
                <!-- Domaćin -->
                <div class="text-center">
                    <div class="mb-1.5">
                        <img
                                v-if="match.home.logo"
                                :src="match.home.logo"
                                :alt="match.home.name"
                                class="w-12 h-12 mx-auto object-contain"
                        />
                        <div
                                v-else
                                class="w-12 h-12 mx-auto bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors"
                        >
              <span class="text-gray-400 font-bold">
                {{ match.home.short_name?.[0] || match.home.name?.[0] }}
              </span>
                        </div>
                    </div>
                    <div class="text-[13px] font-bold text-gray-900 truncate px-1 leading-tight">
                        {{ match.home.short_name || match.home.name }}
                    </div>
                </div>

                <!-- Rezultat -->
                <div class="text-center">
                    <div
                            class="mx-auto inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-2.5 w-full max-w-[110px] hover:border-[#0A2D6B]/20 transition-colors"
                    >
            <span class="text-[28px] font-black text-[#0A2D6B] leading-none">
              {{ displayScore }}
            </span>
                    </div>

                    <div class="mt-1.5 text-[12px] text-gray-600 font-semibold leading-tight">
                        {{ match.competition }}
                    </div>

                    <!-- Dodatni status ako je završeno -->
                    <div class="mt-1">
            <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700"
            >
              {{ $t("matches.finished") }}
            </span>
                    </div>
                </div>

                <!-- Gost -->
                <div class="text-center">
                    <div class="mb-1.5">
                        <img
                                v-if="match.away.logo"
                                :src="match.away.logo"
                                :alt="match.away.name"
                                class="w-12 h-12 mx-auto object-contain"
                        />
                        <div
                                v-else
                                class="w-12 h-12 mx-auto bg-gray-50 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors"
                        >
              <span class="text-gray-400 font-bold">
                {{ match.away.short_name?.[0] || match.away.name?.[0] }}
              </span>
                        </div>
                    </div>
                    <div class="text-[13px] font-bold text-gray-900 truncate px-1 leading-tight">
                        {{ match.away.short_name || match.away.name }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Dugme (Link na Match Centre placeholder) -->
        <div class="px-5 pb-5">
            <RouterLink
                    :to="{ name: 'MatchCentre', params: { id: matchCentreId } }"
                    class="group block w-full rounded-xl bg-gradient-to-r from-[#0A2D6B] to-[#1e40af] text-white py-2.5 text-sm font-bold text-center transition-all shadow-md hover:shadow-lg hover:from-[#1e40af] hover:to-[#0A2D6B]"
            >
        <span class="flex items-center justify-center gap-2">
          {{ $t("matches.matchCentre") }}
          <svg
                  class="w-4 h-4 transition-transform group-hover:translate-x-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </span>
            </RouterLink>
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed} from "vue"
import {RouterLink} from "vue-router"
import {i18n} from "@/i18n"

export type Team = {
    id?: number
    name: string
    short_name?: string
    logo?: string
    city?: string
}

export type LastMatch = {
    id?: number // ✅ dodato da RouterLink uvijek ima nešto smisleno
    home: Team
    away: Team
    score: string
    date: string
    competition: string
    round?: string | null
    kickoff_at?: string
}

const props = defineProps<{ match: LastMatch }>()

const toDateLocale = (loc: string) => {
    if (loc === "sr-Latn") return "sr-Latn-RS"
    if (loc === "sr-Cyrl") return "sr-Cyrl-RS"
    return loc
}

const displayScore = computed(() => {
    const raw = props.match.score ?? ""
    return raw.replace(/\s*:\s*/g, ":").trim()
})

const formattedDate = computed(() => {
    if (!props.match.kickoff_at) return props.match.date

    try {
        const d = new Date(props.match.kickoff_at)
        return d.toLocaleDateString(toDateLocale(i18n.global.locale.value), {
            day: "2-digit",
            month: "long",
            year: "numeric",
        })
    } catch {
        return props.match.date
    }
})

const matchCentreId = computed(() => {
    if (props.match.id != null) return String(props.match.id)
    return props.match.kickoff_at || props.match.date || "0"
})
</script>