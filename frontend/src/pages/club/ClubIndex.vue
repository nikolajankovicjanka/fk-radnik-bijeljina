<script setup lang="ts">
import {computed, onMounted} from "vue"
import {RouterLink} from "vue-router"
import {useNewsStore} from "@/stores/news"

import NewsSliderHomepage from "@/components/news/NewsSliderHomepage.vue"
import {useRevealOnScroll} from "@/composables/useRevealOnScroll"

const newsStore = useNewsStore()
const {setRef: setSectionEl, refresh: refreshSections} = useRevealOnScroll({
    rootMargin: "0px 0px -15% 0px",
    threshold: 0.12,
    once: true,
    visibleClass: "is-visible",
})

const clubNews = computed(() => {
    return (newsStore.items ?? []).filter((n) => n.category === "club").slice(0, 9)
})

onMounted(async () => {
    newsStore.activeCategory = "club"
    if (!newsStore.items.length) await newsStore.load(1)

    await refreshSections()
})
</script>

<template>
    <main class="bg-white">
        <section class="relative overflow-hidden bg-[#071f36]">
            <div class="absolute inset-0">
                <div class="h-full w-full bg-gradient-to-b from-[#0A2D6B] via-[#071f36] to-[#071f36]"/>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:py-20">
                <div class="max-w-3xl">
                    <h1 class="text-white text-4xl sm:text-6xl font-extrabold tracking-tight">
                        {{ $t("pages.clubPage.heroTitle") }}
                    </h1>
                    <p class="mt-5 text-white/85 text-base sm:text-lg leading-relaxed">
                        {{ $t("pages.clubPage.heroDesc") }}
                    </p>
                </div>
            </div>
        </section>

        <!-- SECTION 1: 3 cards -->
        <section
                :ref="setSectionEl"
                class="mx-auto max-w-7xl px-4 py-12 reveal-section"
                aria-label="Club overview links"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <RouterLink :to="{ name: 'ClubGeneralInformation' }"
                            class="club-card">
                    <img
                            src="/club/fk-radnik-general.jpg"
                            :alt="$t('pages.clubPage.cards.general.alt')"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">
                            {{ $t("pages.clubPage.cards.general.title") }}</h3>
                        <p class="club-card-text">
                            {{ $t("pages.clubPage.cards.general.desc") }}</p>
                        <div class="club-card-cta">{{
                                $t("pages.clubPage.learnMore")
                            }} →
                        </div>
                    </div>
                </RouterLink>

                <RouterLink :to="{ name: 'ClubBoard' }" class="club-card">
                    <img
                            src="/club/fk-radnik-uprava.webp"
                            :alt="$t('pages.clubPage.cards.board.alt')"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">
                            {{ $t("pages.clubPage.cards.board.title") }}</h3>
                        <p class="club-card-text">
                            {{ $t("pages.clubPage.cards.board.desc") }}</p>
                        <div class="club-card-cta">{{
                                $t("pages.clubPage.learnMore")
                            }} →
                        </div>
                    </div>
                </RouterLink>

                <RouterLink :to="{ name: 'ClubHistory' }" class="club-card">
                    <img
                            src="/club/fk-radnik-stadion.jpg"
                            :alt="$t('pages.clubPage.cards.history.alt')"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">
                            {{ $t("pages.clubPage.cards.history.title") }}</h3>
                        <p class="club-card-text">
                            {{ $t("pages.clubPage.cards.history.desc") }}</p>
                        <div class="club-card-cta">{{
                                $t("pages.clubPage.learnMore")
                            }} →
                        </div>
                    </div>
                </RouterLink>
            </div>
        </section>

        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <NewsSliderHomepage :title="$t('pages.clubPage.clubNewsTitle')"
                                    category="club" :limit="9"/>

                <div class="mt-8 text-center sm:hidden">
                    <RouterLink
                            :to="{ name: 'News', query: { category: 'club' } }"
                            class="inline-flex items-center gap-2 text-sm font-bold text-[#0A2D6B] hover:underline"
                    >
                        {{ $t("pages.clubPage.allNews") }} →
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- SECTION 3: community/extra -->
        <section
                :ref="setSectionEl"
                class="mx-auto max-w-7xl px-4 py-12 reveal-section"
                aria-label="Club community links"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <RouterLink :to="{ name: 'ClubSupporters' }" class="club-card">
                    <img
                            src="/club/fk-radnik-navijaci.jpg"
                            :alt="$t('pages.clubPage.cards.supporters.alt')"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">
                            {{
                                $t("pages.clubPage.cards.supporters.title")
                            }}</h3>
                        <p class="club-card-text">
                            {{ $t("pages.clubPage.cards.supporters.desc") }}</p>
                        <div class="club-card-cta">{{
                                $t("pages.clubPage.learnMore")
                            }} →
                        </div>
                    </div>
                </RouterLink>

                <RouterLink :to="{ name: 'ClubStadium' }" class="club-card">
                    <img
                            src="/club/fk-radnik-stadion.jpg"
                            :alt="$t('pages.clubPage.cards.stadium.alt')"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">
                            {{ $t("pages.clubPage.cards.stadium.title") }}</h3>
                        <p class="club-card-text">
                            {{ $t("pages.clubPage.cards.stadium.desc") }}</p>
                        <div class="club-card-cta">{{
                                $t("pages.clubPage.learnMore")
                            }} →
                        </div>
                    </div>
                </RouterLink>

                <RouterLink :to="{ name: 'ClubLegends' }" class="club-card">
                    <img
                            src="/club/fk-radnik-general.jpg"
                            :alt="$t('pages.clubPage.cards.legends.alt')"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">
                            {{ $t("pages.clubPage.cards.legends.title") }}</h3>
                        <p class="club-card-text">
                            {{ $t("pages.clubPage.cards.legends.desc") }}</p>
                        <div class="club-card-cta">{{
                                $t("pages.clubPage.learnMore")
                            }} →
                        </div>
                    </div>
                </RouterLink>
            </div>
        </section>
    </main>
</template>

<style scoped></style>
