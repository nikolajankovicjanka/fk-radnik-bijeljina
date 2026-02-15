<script setup lang="ts">
import {computed, onMounted} from "vue"
import {useRevealOnScroll} from "@/composables/useRevealOnScroll"
import SectionLinkCard from "@/components/SectionLinkCard.vue"

const {setRef: setSectionEl, refresh: refreshSections} = useRevealOnScroll({
    rootMargin: "0px 0px -15% 0px",
    threshold: 0.12,
    once: true,
    visibleClass: "is-visible",
})

const cards = computed(() => [
    {
        to: {name: "JuniorTeam"},
        imgSrc: "/club/juniori-hero.jpg",
        titleKey: "pages.youth.cards.juniori.title",
        descKey: "pages.youth.cards.juniori.desc",
        altKey: "pages.youth.cards.juniori.alt",
    },
    {
        to: {name: "KadetiTeam"},
        imgSrc: "/club/kadeti-hero.jpg",
        titleKey: "pages.youth.cards.kadeti.title",
        descKey: "pages.youth.cards.kadeti.desc",
        altKey: "pages.youth.cards.kadeti.alt",
    },
    {
        to: {name: "PioniriTeam"},
        imgSrc: "/club/pioniri-hero.jpg",
        titleKey: "pages.youth.cards.pioniri.title",
        descKey: "pages.youth.cards.pioniri.desc",
        altKey: "pages.youth.cards.pioniri.alt",
    },
])

onMounted(async () => {
    await refreshSections()
})
</script>

<template>
    <main class="bg-white">
        <section
                :ref="setSectionEl"
                class="relative z-10 mx-auto max-w-7xl px-4 -mt-16 sm:-mt-20 reveal-section"
                aria-label="Youth overview links"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <SectionLinkCard
                        v-for="c in cards"
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
