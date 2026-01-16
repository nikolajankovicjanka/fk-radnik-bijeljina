<template>
    <section class="news-section">
        <h2 class="section-title">LATEST NEWS</h2>

        <div class="news-slider">
            <!-- custom arrows -->
            <button ref="prevEl" class="news-nav news-prev">
                <ChevronLeft :size="22" />
            </button>

            <button ref="nextEl" class="news-nav news-next">
                <ChevronRight :size="22" />
            </button>

            <Swiper
                    :modules="[Navigation, Pagination]"
                    :slides-per-view="3"
                    :space-between="24"
                    :loop="true"
                    :navigation="navigationOptions"
                    :pagination="{ clickable: true }"
                    :breakpoints="breakpoints"
                    class="news-swiper"
                    @swiper="onSwiper"
            >
                <SwiperSlide v-for="item in news" :key="item.id">
                    <NewsCardHomepage :item="item" />
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import { Swiper, SwiperSlide } from "swiper/vue"
import { Navigation, Pagination } from "swiper/modules"
import { ChevronLeft, ChevronRight } from "lucide-vue-next"
import "swiper/css"
import "swiper/css/navigation"
import "swiper/css/pagination"

import NewsCardHomepage from "./NewsCardHomepage.vue"
import { getNews } from "../../services/newsService.ts"
import type { NewsItem } from "/../../src/types/news.ts"

const news = ref<NewsItem[]>([])
const isLoading = ref(true)

const breakpoints = {
    0: { slidesPerView: 1 },
    640: { slidesPerView: 1.2 },
    768: { slidesPerView: 2 },
    1024: { slidesPerView: 3 },
}

const prevEl = ref<HTMLElement | null>(null)
const nextEl = ref<HTMLElement | null>(null)
const swiperInstance = ref<any>(null)

const navigationOptions = ref({
    prevEl: null as any,
    nextEl: null as any,
})

function onSwiper(swiper: any) {
    swiperInstance.value = swiper
}

async function loadNews() {
    try {
        isLoading.value = true
        news.value = await getNews()
    } catch (e) {
        console.error("Failed to load news:", e)
        news.value = []
    } finally {
        isLoading.value = false
    }
}

onMounted(async () => {
    await loadNews()

    // poveži custom dugmad tek kad postoje u DOM-u
    navigationOptions.value.prevEl = prevEl.value
    navigationOptions.value.nextEl = nextEl.value

    // re-init navigation (bitno u Vue jer refovi stižu kasnije)
    if (swiperInstance.value?.navigation) {
        swiperInstance.value.navigation.destroy()
        swiperInstance.value.navigation.init()
        swiperInstance.value.navigation.update()
    }
})
</script>

<style scoped>

</style>
