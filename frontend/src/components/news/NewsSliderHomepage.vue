<script setup lang="ts">
import { ref, onMounted, nextTick, watch, computed } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import 'swiper/css'
import 'swiper/css/navigation'

import NewsCardHomepage from './NewsCardHomepage.vue'
import { fetchNews } from '@/services/newsService'
import type { NewsItem, NewsCategory } from '@/types/news'

const props = withDefaults(
    defineProps<{
      title?: string
      category?: NewsCategory | null
      limit?: number
    }>(),
    {
      title: undefined,
      category: null,
      limit: 12,
    }
)

const news = ref<NewsItem[]>([])
const isLoading = ref(true)

const breakpoints = {
  0: { slidesPerView: 1, spaceBetween: 14 },
  640: { slidesPerView: 1.2, spaceBetween: 16 },
  768: { slidesPerView: 2, spaceBetween: 18 },
  1024: { slidesPerView: 3, spaceBetween: 24 },
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

    const res = await fetchNews({
      page: 1,
      perPage: props.limit,
      category: props.category ?? undefined,
    })

    news.value = res.items
  } finally {
    isLoading.value = false
  }
}

async function refreshSwiper() {
  await nextTick()

  if (swiperInstance.value?.navigation) {
    swiperInstance.value.navigation.destroy()
    swiperInstance.value.navigation.init()
    swiperInstance.value.navigation.update()
  }

  swiperInstance.value?.update()
}

onMounted(async () => {
  await loadNews()
  await refreshSwiper()
})

watch(
    [() => props.category, () => props.limit],
    async () => {
      await loadNews()
      await refreshSwiper()
    }
)

const titleText = computed(() => props.title)
</script>

<template>
  <section class="n-section">
    <div class="n-header">
      <h2 class="n-title mb-3">
        {{ titleText ?? $t('home.news.title') }}
        <span class="n-title-arrow">→</span>
      </h2>

      <div class="n-controls">
        <button
            ref="prevEl"
            class="n-nav"
            type="button"
            :aria-label="$t('common.prev')"
        >
          <ChevronLeft :size="20" />
        </button>

        <button
            ref="nextEl"
            class="n-nav n-nav--primary"
            type="button"
            :aria-label="$t('common.next')"
        >
          <ChevronRight :size="20" />
        </button>
      </div>
    </div>

    <div class="n-slider">
      <Swiper
          v-if="news.length"
          :modules="[Navigation]"
          :slides-per-view="3"
          :space-between="24"
          :loop="news.length > 3"
          :breakpoints="breakpoints"
          class="n-swiper"
          @swiper="onSwiper"
          :onBeforeInit="onBeforeInit"
      >
        <SwiperSlide
            v-for="item in news"
            :key="item.id"
            class="n-slide"
        >
          <NewsCardHomepage :item="item" />
        </SwiperSlide>
      </Swiper>

      <div
          v-else-if="!isLoading"
          class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500"
      >
        {{ $t('home.news.empty') }}
      </div>
    </div>
  </section>
</template>