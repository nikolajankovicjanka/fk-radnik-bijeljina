<template>
    <div class="min-w-0 flex flex-col items-center gap-2">
        <div
                class="grid place-items-center overflow-hidden rounded-full border border-slate-200 bg-slate-50
             h-11 w-11 sm:h-14 sm:w-14"
        >
            <img :src="team.logo" alt="" class="h-7 w-7 sm:h-10 sm:w-10 object-contain" />
        </div>

        <div class="min-w-0 text-center text-xs sm:text-sm font-semibold text-slate-800 leading-tight">
      <span class="block truncate max-w-[110px] sm:max-w-[160px]">
        {{ team.name }}
      </span>
        </div>
    </div>
</template>

<script setup lang="ts">
export type Team = { name: string; logo: string }
defineProps<{ team: Team }>()
</script>
