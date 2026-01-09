<template>
    <!-- HERO: full width -->
    <HeroSlider />

    <!-- NEWS: centered container -->
    <section class="relative overflow-hidden
    bg-[linear-gradient(135deg,#1e436c,#05213f_45%,#1e436c)]
    before:absolute before:inset-0 before:content-['']
    before:bg-[linear-gradient(135deg,rgba(80,170,255,0.08)_22%,rgba(80,170,255,0)_42%,rgba(80,170,255,0.10)_63%)]
    before:opacity-100">
        <div class="container mx-auto px-4">
            <NewsSliderHomepage />
        </div>
    </section>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import HeroSlider from '../components/hero/HeroSlider.vue'
import NewsSliderHomepage from '../components/news/NewsSliderHomepage.vue'

export default defineComponent({
    components: {
        HeroSlider,
        NewsSliderHomepage
    }
})
</script>
