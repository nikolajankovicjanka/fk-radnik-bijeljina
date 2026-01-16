import { defineStore } from 'pinia'
import type { NewsItem } from '@/types/news'
import { getNews } from '../src/services/newsService'

type State = {
    items: NewsItem[]
    loading: boolean
    error: string | null
    lastLoadedAt: number | null
}

export const useNewsStore = defineStore('news', {
    state: (): State => ({
        items: [],
        loading: false,
        error: null,
        lastLoadedAt: null,
    }),

    getters: {
        // primjer: sortirano po datumu (pretpostavka ISO date)
        sorted(state): NewsItem[] {
            return [...state.items].sort((a, b) => b.date.localeCompare(a.date))
        },
        bySlug: (state) => {
            return (slug: string) => state.items.find((x) => x.slug === slug)
        },
    },

    actions: {
        async fetchAll(force = false) {
            if (!force && this.items.length > 0) return

            this.loading = true
            this.error = null

            try {
                const data = await getNews()
                this.items = data
                this.lastLoadedAt = Date.now()
            } catch (e) {
                this.error = e instanceof Error ? e.message : 'Failed to load news'
            } finally {
                this.loading = false
            }
        },
    },
})
