<template>
    <section class="sp" aria-label="Sponsors">
        <div class="sp-inner">
            <div class="sp-head">
                <h2 class="n-title mb-3">{{ $t('home.sponsors.sponsors') }}</h2>
            </div>

            <div class="sponsors-grid">
                <a
                        v-for="sponsor in sponsorsData"
                        :key="sponsor.id"
                        class="logo-card"
                        :href="sponsor.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="sponsor.name"
                        :title="sponsor.name"
                >
                    <img
                            :src="sponsor.logo"
                            :alt="sponsor.name"
                            class="logo-img"
                            :class="{ 'logo-lg': sponsor.name === 'Pass' || sponsor.name === 'Kelme'}"
                            loading="lazy"
                    />
                </a>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import {ref} from "vue";

interface Sponsor {
    id: number | string;
    name: string;
    logo: string;
    url: string;
    logoWidth?: number;
    logoHeight?: number;
}

const sponsorsData = ref<Sponsor[]>([
    {id: 1, name: "SoccerBet", logo: "/sponsors/soccerbet-trim.png", url: "#"},
    {id: 2, name: "Pass", logo: "/sponsors/pass-logo.png", url: "#"},
    {id: 3, name: "Autoputevi", logo: "/sponsors/autoputevi-h70.png", url: "#"},
    {id: 4, name: "SoccerBet", logo: "/sponsors/soccerbet-h70.png", url: "#"},
    {id: 5, name: "Kelme", logo: "/sponsors/kelme-trim.png", url: "#"},
    {id: 6, name: "Kelme", logo: "/sponsors/kelme-trim.png", url: "#"},
]);
</script>

<style scoped>

</style>
