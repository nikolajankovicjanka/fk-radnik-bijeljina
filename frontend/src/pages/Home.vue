<template>
    <section class="bg-[#041526]">
        <div class="max-w-7xl mx-auto">
            <HeroSlider />
        </div>
    </section>
    <MainPartnersBar />
    <section class="relative bg-[#ffffff]">
        <div class="container mx-auto px-4">
            <NewsSliderHomepage />
        </div>
    </section>
    <section class="relative p-7 bg-[#071f36]">
        <div class="container mx-auto px-4">
            <MatchTabs />
        </div>
    </section>

    <SeasonTicketsPromo
            imageSrc="/hero/fk-radnik-sezonske.jpg"
            title="Sezonske karte u prodaji"
            subtitle="Osiguraj svoje mjesto na tribinama. Izaberi paket i podrži FK Radnik."
            badgeText="2025/26"
            primaryHref="/tickets"
            primaryText="Kupi sezonsku"
            secondaryHref="/contact"
            secondaryText="Kontakt"
    />

    <SponsorsSection />

</template>

<script lang="ts">
import { defineComponent } from 'vue'
import HeroSlider from '../components/hero/HeroSlider.vue'
import NewsSliderHomepage from '../components/news/NewsSliderHomepage.vue'
import MatchTabs from '../components/matches/MatchTabs.vue'
import SeasonTicketsPromo from "../components/SeasonTicketsPromo.vue";
import MainPartnersBar from "../components/MainPartnersBar.vue";
import SponsorsSection from "../components/SponsorsSection.vue";

export default defineComponent({
    components: {
        SponsorsSection,
        MainPartnersBar,
        MatchTabs,
        HeroSlider,
        NewsSliderHomepage,
        SeasonTicketsPromo
    }
})
</script>
