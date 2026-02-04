<script setup lang="ts">
import {computed} from "vue"
import {useRoute} from "vue-router"
import {useI18n} from "vue-i18n"

const route = useRoute()
const {t} = useI18n()

const heroTitle = computed(() => {
    const key = route.meta.heroTitleKey as string | undefined
    return key ? t(key) : t("pages.clubPage.heroTitle")
})

const heroDesc = computed(() => {
    const key = route.meta.heroDescKey as string | undefined
    return key ? t(key) : t("pages.clubPage.heroDesc")
})
</script>

<template>
    <main class="bg-white">
        <header class="club-hero">
            <div class="club-hero__bg"></div>

            <div class="club-hero__inner mx-auto max-w-7xl px-4">
                <h1>{{ heroTitle }}</h1>
                <p>{{ heroDesc }}</p>
            </div>
        </header>
        <router-view/>
    </main>
</template>

<style scoped>

</style>