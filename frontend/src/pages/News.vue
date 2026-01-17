<script setup lang="ts">
import { onMounted } from "vue";
import { useNewsStore } from "/stores/news";

const store = useNewsStore();

onMounted(() => store.load(1));
</script>

<template>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold">News</h1>

            <select
                    class="bg-white/10 border border-white/10 rounded-md px-3 py-2"
                    :value="store.activeCategory"
                    @change="store.setCategory(($event.target as HTMLSelectElement).value as any)"
            >
                <option value="all">All</option>
                <option value="club">Club</option>
                <option value="first_team">First Team</option>
                <option value="youth">Youth</option>
                <option value="women">Women</option>
            </select>
        </div>

        <div v-if="store.isLoading">Loading...</div>
        <div v-else-if="store.error" class="text-red-400">{{ store.error }}</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <article v-for="n in store.filtered" :key="n.id" class="rounded-xl overflow-hidden bg-[#092441] text-white">
                <img :src="n.image" class="w-full h-48 object-cover" alt="" />
                <div class="p-4">
                    <p class="text-xs opacity-70 mb-1">{{ n.date }}</p>
                    <h3 class="font-semibold mb-2">{{ n.title }}</h3>
                    <p class="text-sm opacity-90 line-clamp-3">{{ n.excerpt }}</p>
                </div>
            </article>
        </div>
    </section>
</template>
