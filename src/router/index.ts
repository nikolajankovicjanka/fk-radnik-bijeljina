import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

import Home from '@/pages/Home.vue'
import About from '@/pages/About.vue'
import News from '@/pages/News.vue'
import Fixtures from '@/pages/Fixtures.vue'
import YouthTeam from '@/pages/YouthTeam.vue'
import WomenTeam from '@/pages/WomenTeam.vue'

const routes: RouteRecordRaw[] = [
    { path: '/', name: 'Home', component: Home },
    { path: '/about', name: 'About', component: About },
    { path: '/news', name: 'News', component: News },
    { path: '/fixtures', name: 'Fixtures', component: Fixtures },
    { path: '/youth-team', name: 'YouthTeam', component: YouthTeam },
    { path: '/women-team', name: 'WomenTeam', component: WomenTeam }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
