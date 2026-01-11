<template>
    <!-- HERO: full width -->
    <HeroSlider />

    <!-- NEWS: centered container -->
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



</template>

<script lang="ts">
import { defineComponent } from 'vue'
import HeroSlider from '../components/hero/HeroSlider.vue'
import NewsSliderHomepage from '../components/news/NewsSliderHomepage.vue'
import MatchTabs from '../components/matches/MatchTabs.vue'

export default defineComponent({
    components: {
        MatchTabs,
        HeroSlider,
        NewsSliderHomepage
    }
})
</script>
