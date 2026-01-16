import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '../layouts/MainLayout.vue'

const routes = [
    {
        path: '/',
        component: MainLayout,
        children: [
            {
                path: '',
                name: 'Home',
                component: () => import('@/pages/Home.vue'),
            },
            {
                path: 'about',
                name: 'About',
                component: () => import('@/pages/About.vue'),
            },
            {
                path: 'news',
                name: 'News',
                component: () => import('@/pages/News.vue'),
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
