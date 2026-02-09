<script setup lang="ts">
import {computed} from "vue"
import {useRoute} from "vue-router"
import {useI18n} from "vue-i18n"

const route = useRoute()
const {t} = useI18n()

const heroTitle = computed(() => {
    const key = route.meta.heroTitleKey as string | undefined
    return key ? t(key) : t("pages.youth.heroTitle")
})

const heroDesc = computed(() => {
    const key = route.meta.heroDescKey as string | undefined
    return key ? t(key) : t("pages.youth.heroDesc")
})

const hasGlass = computed(() => {
    return !!heroImg.value && route.meta.glassHero === true
})

const heroImg = computed(() => (route.meta.heroImg as string | undefined) ?? "")
</script>

<template>
    <main class="bg-white">
        <header class="club-hero"
                :class="{ 'club-hero--has-image': !!heroImg }">

            <div
                    v-if="heroImg"
                    class="club-hero__image"
                    :style="{ backgroundImage: `url(${heroImg})` }"
            ></div>

            <div class="club-hero__bg"></div>

            <div class="club-hero__inner mx-auto max-w-7xl px-4">
                <div v-if="hasGlass" class="club-hero__glass">
                    <h1>{{ heroTitle }}</h1>
                    <p>{{ heroDesc }}</p>
                </div>

                <template v-else>
                    <h1>{{ heroTitle }}</h1>
                    <p>{{ heroDesc }}</p>
                </template>
            </div>
        </header>

        <router-view/>
    </main>
</template>
<style scoped>


</style>