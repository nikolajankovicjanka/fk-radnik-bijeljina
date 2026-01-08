<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, EffectFade, Navigation } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/effect-fade'
import 'swiper/css/navigation'


const slides = [
    {
        title: 'FK Radnik Bijeljina',
        subtitle: 'Igraj za nas, za klub, za grad',
        image: '/hero/fk-radnik-hero-1.jpg',
    },
    {
        title: 'Borba do kraja',
        subtitle: 'Plavo-bijela porodica',
        image: '/hero/fk-radnik-joma.jpg',
    },
    {
        title: 'Borba do kraja',
        subtitle: 'Plavo-bijela Bijeljina',
        image: '/hero/fk-radnik-bozo.jpg',
    },
]
</script>

<template>
    <section class="relative h-[90vh]">
        <Swiper
                :modules="[Autoplay, EffectFade, Navigation]"
                :autoplay="{ delay: 6000, disableOnInteraction: false }"
                effect="fade"
                loop
                :navigation="{
                prevEl: '.swiper-button-prev-custom',
                nextEl: '.swiper-button-next-custom'
                }"
                class="h-full"
        >
            <SwiperSlide v-for="(slide, i) in slides" :key="i">
                <div
                        class="h-full bg-cover bg-center relative"
                        :style="{ backgroundImage: `url(${slide.image})` }"
                >
                    <div class="absolute inset-0 bg-black/60"></div>

                    <div class="relative z-10 h-full flex items-center justify-center text-center px-4">
                        <div class="text-white">
                            <h1 class="text-4xl md:text-6xl font-bold uppercase">
                                {{ slide.title }}
                            </h1>
                            <p class="mt-4 text-lg tracking-wide">
                                {{ slide.subtitle }}
                            </p>
                        </div>
                    </div>
                </div>
            </SwiperSlide>

            <!-- NAV CONTROLS -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 flex items-center gap-6 text-white">
                <button class="swiper-button-prev-custom">
                    ←
                </button>

                <div class="flex gap-2 text-sm tracking-widest">
          <span
                  v-for="(_, index) in slides"
                  :key="index"
                  class="opacity-60"
          >
            {{ String(index + 1).padStart(2, '0') }}
          </span>
                </div>

                <button class="swiper-button-next-custom">
                    →
                </button>
            </div>
        </Swiper>
    </section>
</template>

