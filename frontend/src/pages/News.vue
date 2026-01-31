<script setup lang="ts">
import {onMounted} from 'vue'
import {useNewsStore} from '@/stores/news'
import NewsCardHomepage from '../components/news/NewsCardHomepage.vue'

const store = useNewsStore()

onMounted(() => store.load(1))
</script>

<template>
    <section class="relative overflow-hidden bg-[#071f36]">
        <div class="absolute inset-0">
            <div class="h-full w-full bg-gradient-to-b from-[#0A2D6B] via-[#071f36] to-[#071f36]"/>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:py-20">
            <div class="max-w-3xl">
                <h1 class="text-white text-4xl sm:text-6xl font-extrabold tracking-tight">
                    Vijesti
                </h1>
                <p class="mt-5 text-white/85 text-base sm:text-lg leading-relaxed">
                    Sve vijesti vezane za prvi tim, klub, omladinske selekcije
                    i ženski tim Fk Radnika
                </p>
            </div>
        </div>
    </section>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between gap-4 mb-6">
            <select
                    class="bg-white/10 border border-white/10 rounded-md px-3 py-2"
                    :value="store.activeCategory"
                    @change="store.setCategory(($event.target as HTMLSelectElement).value as any)"
            >
                <option value="all">Sve</option>
                <option value="club">Klub</option>
                <option value="first_team">Prvi tim</option>
                <option value="youth">Omladinske selekcije</option>
                <option value="women">Ženski tim</option>
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
