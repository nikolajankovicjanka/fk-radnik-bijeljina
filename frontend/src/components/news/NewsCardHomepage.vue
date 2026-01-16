<template>
    <article class="card-news">
        <div class="image-wrapper">
            <img :src="item.image" alt="" />
        </div>

        <div class="content">
            <h3>{{ item.title }}</h3>
            <span class="date">{{ item.date }}</span>
            <button class="button-news">READ MORE</button>
        </div>
    </article>
</template>

<script setup lang="ts">
defineProps<{
    item: {
        title: string
        image: string
        date: string
    }
}>()
</script>

<style scoped>

</style>
