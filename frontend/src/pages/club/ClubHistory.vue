<script setup lang="ts">
import {computed} from "vue"
import {useI18n} from "vue-i18n"

type HistoryBlock = {
    label: string
    title: string
    text: string
}

const {t} = useI18n()

const blocks = computed<HistoryBlock[]>(() => [
    {
        label: t("club.clubHistory.blocks.0.label"),
        title: t("club.clubHistory.blocks.0.title"),
        text: t("club.clubHistory.blocks.0.text"),
    },
    {
        label: t("club.clubHistory.blocks.1.label"),
        title: t("club.clubHistory.blocks.1.title"),
        text: t("club.clubHistory.blocks.1.text"),
    },
    {
        label: t("club.clubHistory.blocks.2.label"),
        title: t("club.clubHistory.blocks.2.title"),
        text: t("club.clubHistory.blocks.2.text"),
    },
    {
        label: t("club.clubHistory.blocks.3.label"),
        title: t("club.clubHistory.blocks.3.title"),
        text: t("club.clubHistory.blocks.3.text"),
    },
])
</script>

<template>
    <section class="relative bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-10 md:py-14">
            <!-- Intro -->
            <div class="relative max-w-4xl">
                <div class="pl-6">
                    <!-- Badge -->
                    <div
                            class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-1.5 text-xs font-semibold tracking-wide text-[#0A2D6B] ring-1 ring-slate-200"
                    >
                        <span class="h-2 w-2 rounded-full bg-[#0A2D6B]"></span>
                        {{ $t("club.clubHistory.badge") }}
                    </div>

                    <!-- Title -->
                    <h2 class="mt-4 text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
                        {{ $t("club.clubHistory.title") }}
                    </h2>

                    <!-- Blue underline -->
                    <div class="mt-3 h-[3px] w-16 bg-[#0A2D6B] rounded-full"></div>

                    <!-- Description -->
                    <p class="mt-5 text-lg leading-relaxed text-slate-600 max-w-3xl">
                        {{ $t("club.clubHistory.desc") }}
                    </p>
                </div>
            </div>

            <!-- Cards -->
            <div class="mt-8 grid gap-4 md:gap-5">
                <article
                        v-for="(b, i) in blocks"
                        :key="i"
                        class="group rounded-2xl bg-[#0f2a63] ring-1 ring-[#173a8c] shadow-sm transition-all duration-200 hover:-translate-y-[2px] hover:shadow-xl hover:shadow-blue-500/20"
                >
                    <div class="p-5 md:p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                            <div>
                                <!-- TITLE -->
                                <h3 class="text-xl md:text-2xl font-semibold text-white">
                                    {{ b.title }}
                                </h3>

                                <!-- BLUE LINE -->
                                <div class="mt-2 h-[3px] w-12 bg-blue-400 rounded-full"></div>
                            </div>

                            <!-- LABEL -->
                            <div
                                    class="inline-flex w-fit items-center rounded-full bg-white/10 px-3 py-1 text-xs md:text-sm text-white/90 ring-1 ring-white/15"
                            >
                                {{ b.label }}
                            </div>
                        </div>

                        <p class="mt-4 text-white/80 leading-relaxed">
                            {{ b.text }}
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<style scoped></style>