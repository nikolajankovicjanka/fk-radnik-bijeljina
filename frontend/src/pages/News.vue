<script setup lang="ts">
import {onMounted} from 'vue'
import {useNewsStore} from '@/stores/news'
import NewsCardHomepage from '../components/news/NewsCardHomepage.vue'

const store = useNewsStore()

onMounted(() => store.load(1))
</script>

<template>
    <section class="relative overflow-hidden bg-[#071f36]">
        <header class="club-hero">
            <div class="club-hero__bg"></div>

            <div class="club-hero__inner mx-auto max-w-7xl px-4">
                <h1 class="text-white text-4xl sm:text-6xl font-extrabold tracking-tight">
                    {{ $t('pages.newsPage.heroTitle') }}
                </h1>
                <p class="mt-5 text-white/85 text-base sm:text-lg leading-relaxed">
                    {{ $t('pages.newsPage.heroDesc') }}
                </p>
            </div>
        </header>
    </section>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between gap-4 mb-6">
            <select
                    class="appearance-none  bg-gradient-to-r from-blue-50/60 to-white border border-blue-200/50 text-[#0b2a55]  font-semibold text-sm

      px-4 py-2.5 pr-10
      rounded-lg

      shadow-sm
      hover:border-blue-300/70
      hover:bg-blue-50/70

      focus:outline-none
      focus:ring-2 focus:ring-blue-300/40
      focus:border-blue-400/60

      transition
    "
                    :value="store.activeCategory"
                    @change="store.setCategory(($event.target as HTMLSelectElement).value as any)"
            >
                <option value="all">{{
                        $t('pages.newsPage.categories.all')
                    }}
                </option>
                <option value="club">{{
                        $t('pages.newsPage.categories.club')
                    }}
                </option>
                <option value="first_team">
                    {{ $t('pages.newsPage.categories.first_team') }}
                </option>
                <option value="youth">{{
                        $t('pages.newsPage.categories.youth')
                    }}
                </option>
                <option value="women">{{
                        $t('pages.newsPage.categories.women')
                    }}
                </option>
            </select>
        </div>

        <div v-if="store.isLoading">Loading...</div>
        <div v-else-if="store.error" class="text-red-400">{{
                store.error
            }}
        </div>

        <div v-else
             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <NewsCardHomepage v-for="n in store.filtered" :key="n.id"
                              :item="n"/>
        </div>
    </section>
</template>
