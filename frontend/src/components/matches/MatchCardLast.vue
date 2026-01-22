<template>
  <div class="rounded-2xl bg-white shadow-[0_18px_40px_rgba(0,0,0,0.18)] overflow-hidden">
    <!-- header -->
    <div class="px-4 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="text-[11px] font-extrabold tracking-widest text-slate-500 uppercase">
        LAST MATCH
      </div>
      <div class="text-[11px] font-semibold text-slate-400">
        {{ match.date }}
      </div>
    </div>

    <!-- content -->
    <div class="px-4 sm:px-6 py-5 sm:py-6">
      <div class="min-w-0 grid grid-cols-3 items-center gap-3 sm:gap-4">
        <TeamBlock :team="match.home" />

        <!-- score -->
        <div class="text-center min-w-0">
          <div
            class="mx-auto inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 sm:px-5 py-3 w-full max-w-[92px] sm:max-w-[120px]"
          >
            <span
              class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0A2D6B] leading-none"
            >
              {{ match.score }}
            </span>
          </div>

          <div class="mt-2 text-[11px] sm:text-[12px] text-slate-500 font-semibold">
            {{ match.competition }}
          </div>
        </div>

        <TeamBlock :team="match.away" />
      </div>
    </div>

    <!-- button -->
    <div class="px-4 sm:px-6 pb-6">
      <button
        type="button"
        class="w-full rounded-xl bg-[#0A2D6B] py-2.5 sm:py-3 text-white font-bold text-sm hover:brightness-105 transition"
      >
        Match Centre →
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
  import TeamBlock, { type Team } from './TeamBlock.vue'

  export type LastMatch = {
    home: Team
    away: Team
    score: string
    date: string
    competition: string
  }

  defineProps<{ match: LastMatch }>()
</script>
