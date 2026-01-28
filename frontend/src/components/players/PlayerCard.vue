<script setup lang="ts">
type Player = {
    id: number | string
    name: string
    photo?: string | null
    shirt_number?: number | null
    position?: string | null
}

const props = defineProps<{
    player: Player
    setCardEl?: (el: Element | null) => void
}>()
</script>

<template>
    <article
            :ref="setCardEl"
            class="group relative overflow-hidden rounded-2xl border border-gray-200 shadow-lg bg-white reveal-card"
    >
        <!-- IMAGE -->
        <img
                :src="player.photo ?? '/players/placeholder.png'"
                :alt="player.name"
                class="h-[360px] sm:h-[420px] w-full
             object-cover object-top
             transition duration-500
             group-hover:scale-[1.03] group-hover:blur-[2px]"
        />

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>

        <!-- Bottom name (default state) -->
        <div
                class="absolute inset-x-0 bottom-0 pb-4 px-4 text-center transition duration-300 group-hover:opacity-0"
        >
            <div class="relative inline-block">
        <span class="text-base sm:text-lg font-extrabold text-white drop-shadow">
          {{ player.name }}
        </span>

                <span
                        class="absolute left-0 -bottom-1 h-[3px] w-full bg-[#3332c9]
                 origin-left scale-x-0
                 transition-transform duration-500 ease-out
                 group-hover:scale-x-100"
                />
            </div>
        </div>

        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center
             opacity-0 transition duration-300 group-hover:opacity-100">
            <div class="absolute inset-0 bg-black/45"></div>

            <div class="relative text-center px-4">
                <div class="mt-2 text-6xl sm:text-7xl font-black tracking-tight text-white drop-shadow">
                    {{ player.shirt_number ?? "—" }}
                </div>
                <div class="mt-1 text-xs sm:text-sm font-extrabold uppercase tracking-[0.25em] text-white/85">
                    {{ player.position ?? "Player" }}
                </div>
            </div>

            <div class="relative inline-block">
        <span class="text-lg sm:text-xl font-extrabold text-white drop-shadow">
          {{ player.name }}
        </span>

                <span
                        class="absolute left-0 -bottom-1 h-[3px] w-full bg-[#3332c9]
                 origin-left scale-x-0
                 transition-transform duration-500 ease-out
                 group-hover:scale-x-100"
                />
            </div>
        </div>
    </article>
</template>

