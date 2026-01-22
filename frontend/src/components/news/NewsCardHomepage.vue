<template>
  <RouterLink class="c-card" :to="{ name: 'NewsSingle', params: { slug: item.slug } }">
    <div class="c-media">
      <img :src="item.image" :alt="item.title" loading="lazy" />
    </div>

    <div class="c-body">
      <h3 class="c-title">{{ item.title }}</h3>

      <p v-if="item.excerpt" class="c-desc">
        {{ item.excerpt }}
      </p>
    </div>

    <div class="c-meta">
      <span class="c-category">{{ item.categoryLabel }}</span>
      <span class="c-time">{{ item.date }}</span>
    </div>
  </RouterLink>
</template>

<script setup lang="ts">
  import type { NewsItem } from '@/types/news'
  defineProps<{ item: NewsItem }>()
</script>

<style scoped></style>
