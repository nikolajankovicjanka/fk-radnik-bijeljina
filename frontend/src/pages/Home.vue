<template>
    <section class="bg-[#041526]">
        <div class="max-w-7xl mx-auto py-2">
            <HeroSlider/>
        </div>
    </section>
    <MainPartnersBar/>
    <section class="relative bg-[#ffffff]">
        <div class="container mx-auto px-4">
            <NewsSliderHomepage/>
        </div>
    </section>
    <section class="relative p-7 bg-[#071f36]">
        <div class="container mx-auto px-4">
            <MatchTabs/>
        </div>
    </section>
    <SeasonTicketPopup
        image-src="/hero/fk-radnik-sezonske.png"
        image-alt="Sezonske karte FK Radnik Bijeljina 2026/27"
        ticket-email="office@fkradnikbijeljina.com"
        phone-number="+387XXXXXXXX"
        :duration="6000"
        storage-key="fk-radnik-season-ticket-popup-2026-27"
    />




  <SeasonTicketsPromo
        image-src="/hero/fk-radnik-sezonske.png"
        title="Sezonske karte u prodaji"
        subtitle="Osiguraj svoje mjesto na tribinama. Izaberi paket i podrži FK Radnik."
        badge-text="2026/27"
        ticket-email="office@fkradnikbijeljina.com"
        phone-number="+38765691808"
        primary-text="Kupi sezonsku"
        secondary-text="Kontakt"
    />

    <SponsorsSection/>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import HeroSlider from '../components/hero/HeroSlider.vue'
import NewsSliderHomepage from '../components/news/NewsSliderHomepage.vue'
import MatchTabs from '../components/matches/MatchTabs.vue'
import SeasonTicketsPromo from '../components/SeasonTicketsPromo.vue'
import SeasonTicketPopup from '@/components/SeasonTicketPopup.vue'
import MainPartnersBar from '../components/MainPartnersBar.vue'
import SponsorsSection from '../components/SponsorsSection.vue'

export default defineComponent({
    components: {
        SponsorsSection,
        MainPartnersBar,
        MatchTabs,
        SeasonTicketPopup,
        HeroSlider,
        NewsSliderHomepage,
        SeasonTicketsPromo,
    },
})
</script>
