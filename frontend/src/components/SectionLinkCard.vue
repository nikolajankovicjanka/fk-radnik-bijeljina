<script setup lang="ts">
import {RouterLink, type RouteLocationRaw} from "vue-router"

type Props = {
    to: RouteLocationRaw
    imgSrc: string
    imgAlt: string
    title: string
    desc: string
    cta?: string
}

withDefaults(defineProps<Props>(), {
    cta: "Learn more",
})
</script>

<template>
    <RouterLink :to="to" class="club-card">
        <img :src="imgSrc" :alt="imgAlt" class="club-card-img" loading="lazy"/>
        <div class="club-card-body">
            <h3 class="club-card-title">{{ title }}</h3>
            <p class="club-card-text">{{ desc }}</p>
            <div class="club-card-cta">{{ cta }} →</div>
        </div>
    </RouterLink>
</template>
