<script setup lang="ts">
import {ref, onMounted, nextTick} from 'vue'
import {Swiper, SwiperSlide} from 'swiper/vue'
import {Navigation, Pagination} from 'swiper/modules'
import {ChevronLeft, ChevronRight} from 'lucide-vue-next'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'

import NewsCardHomepage from './NewsCardHomepage.vue'
import {getNews} from '../../services/newsService'
import type {NewsItem} from '@/types/news'

const news = ref<NewsItem[]>([])
const isLoading = ref(true)

const breakpoints = {
    0: {slidesPerView: 1, spaceBetween: 14},
    640: {slidesPerView: 1.2, spaceBetween: 16},
    768: {slidesPerView: 2, spaceBetween: 18},
    1024: {slidesPerView: 3, spaceBetween: 24},
}

const prevEl = ref<HTMLElement | null>(null)
const nextEl = ref<HTMLElement | null>(null)
const swiperInstance = ref<any>(null)

function onSwiper(swiper: any) {
    swiperInstance.value = swiper
}

function onBeforeInit(swiper: any) {
    swiper.params.navigation = swiper.params.navigation || {}
    swiper.params.navigation.prevEl = prevEl.value
    swiper.params.navigation.nextEl = nextEl.value
}

async function loadNews() {
    try {
        isLoading.value = true
        news.value = await getNews()
    } finally {
        isLoading.value = false
    }
}

onMounted(async () => {
    await loadNews()
    await nextTick()

    if (swiperInstance.value?.navigation) {
        swiperInstance.value.navigation.destroy()
        swiperInstance.value.navigation.init()
        swiperInstance.value.navigation.update()
    }
})
</script>

<template>
    <section class="n-section">
        <div class="n-header">
            <h2 class="n-title mb-3">LATEST NEWS <span
                    class="n-title-arrow">→</span></h2>

            <div class="n-controls">
                <button ref="prevEl" class="n-nav" type="button"
                        aria-label="Previous">
                    <ChevronLeft :size="20"/>
                </button>
                <button ref="nextEl" class="n-nav n-nav--primary" type="button"
                        aria-label="Next">
                    <ChevronRight :size="20"/>
                </button>
            </div>
        </div>

        <div class="n-slider">
            <Swiper
                    :modules="[Navigation]"
                    :slides-per-view="3"
                    :space-between="24"
                    :loop="news.length > 3"
                    :pagination="{ clickable: true }"
                    :breakpoints="breakpoints"
                    class="n-swiper"
                    @swiper="onSwiper"
                    :onBeforeInit="onBeforeInit"
            >
                <SwiperSlide v-for="item in news" :key="item.id"
                             class="n-slide">
                    <NewsCardHomepage :item="item"/>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>
