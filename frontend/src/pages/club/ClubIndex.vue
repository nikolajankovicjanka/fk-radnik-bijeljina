<script setup lang="ts">
import {computed, onMounted} from "vue"
import {RouterLink} from "vue-router"
import {useNewsStore} from "@/stores/news"

import NewsSliderHomepage from "@/components/news/NewsSliderHomepage.vue"
import {useRevealOnScroll} from "@/composables/useRevealOnScroll"
import SectionLinkCard from "@/components/SectionLinkCard.vue"

const newsStore = useNewsStore()

const {setRef: setSectionEl, refresh: refreshSections} = useRevealOnScroll({
    rootMargin: "0px 0px -15% 0px",
    threshold: 0.12,
    once: true,
    visibleClass: "is-visible",
})

const topCards = computed(() => [
    {
        to: {name: "ClubGeneralInformation"},
        imgSrc: "/club/fk-radnik-general.jpg",
        imgAlt: (window as any)?.$t ? "" : "",
        titleKey: "pages.clubPage.cards.general.title",
        descKey: "pages.clubPage.cards.general.desc",
        altKey: "pages.clubPage.cards.general.alt",
    },
    {
        to: {name: "ClubBoard"},
        imgSrc: "/club/fk-radnik-uprava.webp",
        titleKey: "pages.clubPage.cards.board.title",
        descKey: "pages.clubPage.cards.board.desc",
        altKey: "pages.clubPage.cards.board.alt",
    },
    {
        to: {name: "ClubHistory"},
        imgSrc: "/club/fk-radnik-stadion.jpg",
        titleKey: "pages.clubPage.cards.history.title",
        descKey: "pages.clubPage.cards.history.desc",
        altKey: "pages.clubPage.cards.history.alt",
    },
])

const bottomCards = computed(() => [
    {
        to: {name: "ClubSupporters"},
        imgSrc: "/club/fk-radnik-navijaci.jpg",
        titleKey: "pages.clubPage.cards.supporters.title",
        descKey: "pages.clubPage.cards.supporters.desc",
        altKey: "pages.clubPage.cards.supporters.alt",
    },
    {
        to: {name: "ClubStadium"},
        imgSrc: "/club/fk-radnik-stadion.jpg",
        titleKey: "pages.clubPage.cards.stadium.title",
        descKey: "pages.clubPage.cards.stadium.desc",
        altKey: "pages.clubPage.cards.stadium.alt",
    },
    {
        to: {name: "ClubLegends"},
        imgSrc: "/club/fk-radnik-general.jpg",
        titleKey: "pages.clubPage.cards.legends.title",
        descKey: "pages.clubPage.cards.legends.desc",
        altKey: "pages.clubPage.cards.legends.alt",
    },
])

onMounted(async () => {
    newsStore.activeCategory = "club"
    if (!newsStore.items.length) await newsStore.load(1)
    await refreshSections()
})
</script>

<template>
    <main class="bg-white">
        <!-- SECTION 1 -->
        <section
                :ref="setSectionEl"
                class="relative z-10 mx-auto max-w-7xl px-4 -mt-16 sm:-mt-20 reveal-section"
                aria-label="Club overview links"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <SectionLinkCard
                        v-for="c in topCards"
                        :key="String((c.to as any).name)"
                        :to="c.to"
                        :img-src="c.imgSrc"
                        :img-alt="$t(c.altKey)"
                        :title="$t(c.titleKey)"
                        :desc="$t(c.descKey)"
                        :cta="$t('pages.clubPage.learnMore')"
                />
            </div>
        </section>

        <!-- SECTION 2 -->
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-5 py-12">
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

        <!-- SECTION 3 -->
        <section
                :ref="setSectionEl"
                class="mx-auto max-w-7xl px-4 py-12 reveal-section"
                aria-label="Club community links"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <SectionLinkCard
                        v-for="c in bottomCards"
                        :key="String((c.to as any).name)"
                        :to="c.to"
                        :img-src="c.imgSrc"
                        :img-alt="$t(c.altKey)"
                        :title="$t(c.titleKey)"
                        :desc="$t(c.descKey)"
                        :cta="$t('pages.clubPage.learnMore')"
                />
            </div>
        </section>
    </main>
</template>

<style scoped></style>
