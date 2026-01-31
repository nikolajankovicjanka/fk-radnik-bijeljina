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
    return (newsStore.items ?? []).filter(n => n.category === "club").slice(0, 9)
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
                        O klubu
                    </h1>
                    <p class="mt-5 text-white/85 text-base sm:text-lg leading-relaxed">
                        Osnovne informacije o FK Radnik Bijeljina, upravi kluba,
                        istoriji i
                        svemu što čini klub.
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
                <RouterLink to="/general-information" class="club-card">
                    <img
                            src="/club/fk-radnik-general.jpg"
                            alt="Osnovne informacije"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">Osnovne informacije</h3>
                        <p class="club-card-text">
                            Stadion, boje kluba, organizacija, dokumenti i
                            ključne informacije.
                        </p>
                        <div class="club-card-cta">Saznaj više →</div>
                    </div>
                </RouterLink>

                <RouterLink to="/club-board" class="club-card">
                    <img
                            src="/club/fk-radnik-uprava.webp"
                            alt="Uprava kluba"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">Uprava kluba</h3>
                        <p class="club-card-text">
                            Uprava, rukovodstvo i organizaciona struktura kluba.
                        </p>
                        <div class="club-card-cta">Saznaj više →</div>
                    </div>
                </RouterLink>

                <RouterLink to="/club-history" class="club-card">
                    <img
                            src="/club/fk-radnik-stadion.jpg"
                            alt="Istorija Radnika"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">Istorija Radnika</h3>
                        <p class="club-card-text">
                            Najvažniji trenuci, uspjesi i priča kluba kroz
                            decenije.
                        </p>
                        <div class="club-card-cta">Saznaj više →</div>
                    </div>
                </RouterLink>
            </div>
        </section>

        <!-- SECTION 2: Club news -->
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <NewsSliderHomepage
                        title="Vijesti o klubu"
                        category="club"
                        :limit="9"
                />

                <div class="mt-8 text-center sm:hidden">
                    <RouterLink
                            :to="{ name: 'News', query: { category: 'club' } }"
                            class="inline-flex items-center gap-2 text-sm font-bold text-[#0A2D6B] hover:underline"
                    >
                        Sve vijesti →
                    </RouterLink>
                </div>
            </div>
        </section>

        <section
                :ref="setSectionEl"
                class="mx-auto max-w-7xl px-4 py-12 reveal-section"
                aria-label="Club community links"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <RouterLink to="/club-supporters" class="club-card">
                    <img
                            src="/club/fk-radnik-navijaci.jpg"
                            alt="Radnikovi navijači"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">Radnikovi navijači</h3>
                        <p class="club-card-text">
                            Priča o navijačima, tribini i atmosferi koja nosi
                            klub.
                        </p>
                        <div class="club-card-cta">Saznaj više →</div>
                    </div>
                </RouterLink>

                <RouterLink to="/club-stadium" class="club-card">
                    <img
                            src="/club/fk-radnik-stadion.jpg"
                            alt="Stadion Radnika"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">Stadion Radnika</h3>
                        <p class="club-card-text">
                            Informacije o stadionu, lokaciji, kapacitetu i
                            istoriji.
                        </p>
                        <div class="club-card-cta">Saznaj više →</div>
                    </div>
                </RouterLink>

                <RouterLink to="/club-legends" class="club-card">
                    <img
                            src="/club/fk-radnik-general.jpg"
                            alt="Radnikove legende"
                            class="club-card-img"
                    />
                    <div class="club-card-body">
                        <h3 class="club-card-title">Radnikove legende</h3>
                        <p class="club-card-text">
                            Igrači, treneri i ljudi koji su ostavili trag u
                            klubu.
                        </p>
                        <div class="club-card-cta">Saznaj više →</div>
                    </div>
                </RouterLink>
            </div>
        </section>
    </main>
</template>

<style scoped>
</style>
