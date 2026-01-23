import {createRouter, createWebHistory} from 'vue-router'
import MainLayout from '../layouts/MainLayout.vue'
import {useNewsStore} from "@/stores/news"
import {useGamesStore} from "@/stores/games"
import {usePlayersStore} from "@/stores/players"

const routes = [
    {
        path: '/',
        component: MainLayout,
        beforeEnter: async () => {
            const newsStore = useNewsStore()
            const gamesStore = useGamesStore()
            const playersStore = usePlayersStore()

            await Promise.all([
                newsStore.load(1),
                gamesStore.load(50),
                playersStore.load(200),
            ])
        },
        children: [
            {
                path: '',
                name: 'Home',
                component: () => import('@/pages/Home.vue'),
            },
            {
                path: 'first-team',
                name: 'First Team',
                component: () => import('@/pages/FirstTeam.vue'),
            },
            {
                path: 'club',
                name: 'Club',
                component: () => import('@/pages/Club.vue'),
            },
            {
                path: 'news',
                name: 'News',
                component: () => import('@/pages/News.vue'),
            },
            {
                path: 'news/:slug',
                name: 'NewsSingle',
                component: () => import('@/pages/NewsSingle.vue'),
                props: true,
            },
            {
                path: 'fixtures',
                name: 'Fixtures',
                component: () => import('@/pages/Fixtures.vue'),
            },
            {
                path: 'youth-team',
                name: 'YouthTeam',
                component: () => import('@/pages/YouthTeam.vue'),
            },
            {
                path: 'women-team',
                name: 'WomenTeam',
                component: () => import('@/pages/WomenTeam.vue'),
            },
        ],
    },
]


const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
