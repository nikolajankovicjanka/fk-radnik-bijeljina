<template>
    <article class="news-single w-full object-cover object-top">
        <section v-if="isLoading" class="content">
            <div class="container">Loading...</div>
        </section>

        <!-- ERROR -->
        <section v-else-if="error" class="content">
            <div class="container text-red">{{ error }}</div>
        </section>

        <!-- EMPTY (nije nađeno) -->
        <section v-else-if="!item" class="content">
            <div class="container">Vijest nije pronađena.</div>
        </section>

        <!-- ✅ RENDER ONLY WHEN item EXISTS -->
        <template v-else>
            <header class="hero">
                <div class="hero-media">
                    <img :src="item.image" :alt="item.title"/>
                </div>

                <div class="hero-overlay"></div>

                <div class="hero-inner">
                    <div class="hero-meta">
                        <span class="tag">{{ item.categoryLabel }}</span>
                        <span class="time">{{ item.date }}</span>
                    </div>

                    <h1 class="hero-title">{{ item.title }}</h1>

                    <p v-if="item.excerpt" class="hero-lead">{{
                            item.excerpt
                        }}</p>

                    <div class="hero-divider"></div>
                </div>
            </header>

            <section class="news-card-content">
                <div class="container">
                    <div v-if="item.content" class="rich"
                         v-html="item.content"></div>
                    <p v-else class="body-text">Nema sadržaja za ovu vijest.</p>
                </div>
            </section>
        </template>
    </article>
</template>

<script setup lang="ts">
import {onMounted, ref, watch} from 'vue'
import type {NewsItem} from '@/types/news'
import {getNewsBySlug} from '@/services/newsService'

const props = defineProps<{ slug: string }>()
const item = ref<(NewsItem & { content?: string }) | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function load(slug: string) {
    try {
        isLoading.value = true
        error.value = null
        item.value = null
        item.value = await getNewsBySlug(slug)
    } catch (e: any) {
        error.value = e?.message ?? 'Failed to load news'
        item.value = null
    } finally {
        isLoading.value = false
    }
}

onMounted(() => load(props.slug))
watch(
    () => props.slug,
    s => load(s)
)
</script>

<style scoped>
.news-single {
    background: #ffffff;
}

/* HERO */
.hero {
    position: relative;
    min-height: 600px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
}

.hero-media {
    position: absolute;
    inset: 0;
}

.hero-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: 45% 35%;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
            180deg,
            rgba(0, 0, 0, 0.2) 0%,
            rgba(0, 0, 0, 0.7) 65%,
            rgba(0, 0, 0, 0.88) 100%
    );
}

.hero-inner {
    position: relative;
    width: min(1100px, calc(100% - 40px));
    margin: 0 auto;
    padding: 0 0 34px;
    color: #fff;
}

.hero-meta {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 12px;
}

.tag {
    font-size: 0.78rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #ffd24a;
}

.time {
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    opacity: 0.85;
    margin-left: auto;
}

.hero-title {
    margin: 0;
    font-size: clamp(2.1rem, 4vw, 3.6rem);
    font-weight: 950;
    letter-spacing: -0.02em;
    line-height: 1.05;
    max-width: 900px;
}

.hero-lead {
    margin: 14px 0 0;
    font-size: 1.05rem;
    line-height: 1.6;
    opacity: 0.9;
    max-width: 780px;
}

.hero-divider {
    margin-top: 20px;
    height: 1px;
    width: 100%;
    background: rgba(255, 255, 255, 0.22);
}

.content-news {
    padding: 34px 0 70px;
}

.container {
    width: min(900px, calc(100% - 40px));
    margin: 0 auto;
}

.body-text,
.rich {
    font-size: 1.05rem;
    line-height: 1.9;
    color: rgba(10, 36, 94, 0.88);
}

.rich :deep(p) {
    margin: 0 0 18px;
}

.rich :deep(h2) {
    margin: 26px 0 10px;
    font-size: 1.6rem;
}

.rich :deep(img) {
    max-width: 100%;
    border-radius: 12px;
}

@media (max-width: 640px) {
    .hero {
        min-height: 480px;
    }

    .hero-inner {
        padding-bottom: 24px;
    }

    .time {
        display: none;
    }
}
</style>
