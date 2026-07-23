<template>
  <section class="bg-[#041526]">
    <div class="mx-auto max-w-7xl py-2">
      <HeroSlider />
    </div>
  </section>

  <MainPartnersBar />

  <section class="relative bg-white">
    <div class="container mx-auto px-4">
      <NewsSliderHomepage />
    </div>
  </section>

  <section class="relative bg-[#071f36] p-7">
    <div class="container mx-auto px-4">
      <MatchTabs />
    </div>
  </section>

  <SeasonTicketPopup
      image-src="/hero/fk-radnik-sezonske.png"
      title="Sezonske karte u prodaji"
      subtitle="Osiguraj svoje mjesto na tribinama. Izaberi paket i podrži FK Radnik."
      badge-text="2026/27"
      ticket-email="office@fkradnikbijeljina.com"
      phone-number="+38765691808"
      primary-text="Kupi sezonsku"
      secondary-text="Kontakt"
      :duration="10000"
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

  <SponsorsSection />
</template>

<script setup lang="ts">
import HeroSlider from '../components/hero/HeroSlider.vue'
import NewsSliderHomepage from '../components/news/NewsSliderHomepage.vue'
import MatchTabs from '../components/matches/MatchTabs.vue'
import SeasonTicketsPromo from '../components/SeasonTicketsPromo.vue'
import SeasonTicketPopup from '../components/SeasonTicketPopup.vue'
import MainPartnersBar from '../components/MainPartnersBar.vue'
import SponsorsSection from '../components/SponsorsSection.vue'
</script>