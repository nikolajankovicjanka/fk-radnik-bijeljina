<script setup lang="ts">
import { onMounted } from "vue";
import { useNewsStore } from "/src/stores/news";
import NewsCardHomepage from "../components/news/NewsCardHomepage.vue";

const store = useNewsStore();

onMounted(() => store.load(1));
</script>

<template>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h1 class="n-title">Vijesti</h1>

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
        <div v-else-if="store.error" class="text-red-400">{{ store.error }}</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <NewsCardHomepage
                    v-for="n in store.filtered"
                    :key="n.id"
                    :item="n"
            />
        </div>
    </section>
</template>
