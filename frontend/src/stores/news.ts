import {defineStore} from 'pinia'
import type {NewsItem, NewsCategory} from '@/types/news'
import {fetchNews} from '@/services/newsService'

type Pagination = {
    page: number
    perPage: number
    total: number
    lastPage: number
}

export const useNewsStore = defineStore('news', {
    state: () => ({
        items: [] as NewsItem[],
        isLoading: false,
        error: null as string | null,
        pagination: {
            page: 1,
            perPage: 9,
            total: 0,
            lastPage: 1,
        } as Pagination,
        activeCategory: 'first_team' as NewsCategory | 'all',
        query: '' as string,
    }),

    getters: {
        filtered(state) {
            const byCategory =
                state.activeCategory === 'all'
                    ? state.items
                    : state.items.filter(n => n.category === state.activeCategory)

            const q = state.query.trim().toLowerCase()
            if (!q) return byCategory

            return byCategory.filter(
                n => n.title.toLowerCase().includes(q) || n.excerpt.toLowerCase().includes(q)
            )
        },
    },

    actions: {
        async load(page = 1) {
            try {
                this.isLoading = true
                this.error = null

                const res = await fetchNews({
                    page,
                    perPage: this.pagination.perPage,
                    q: this.query,
                    category: this.activeCategory === 'all' ? undefined : this.activeCategory,
                })

                this.items = res.items
                this.pagination.page = res.page
                this.pagination.perPage = res.perPage
                this.pagination.total = res.total
                this.pagination.lastPage = res.lastPage
            } catch (e: any) {
                this.error = e?.message ?? 'Failed to load news'
            } finally {
                this.isLoading = false
            }
        },

        setCategory(cat: NewsCategory | 'all') {
            this.activeCategory = cat
            this.load(1)
        },

        setQuery(q: string) {
            this.query = q
            // ne reload odmah dok kucaš? može debounce kasnije
        },
    },
})
