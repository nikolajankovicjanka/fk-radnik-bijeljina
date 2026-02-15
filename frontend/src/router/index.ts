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

            await Promise.all([
                newsStore.load(1),
                gamesStore.loadHomepageTabs()
            ])
        },
        children: [
            {
                path: '',
                name: 'Home',
                component: () => import('@/pages/Home.vue'),
            },
            {
                path: "first-team",
                name: "First Team",
                component: () => import("@/pages/FirstTeam.vue"),
                beforeEnter: async () => {
                    const newsStore = useNewsStore()
                    const gamesStore = useGamesStore()
                    const playersStore = usePlayersStore()

                    if (newsStore.activeCategory !== "first_team") {
                        newsStore.activeCategory = "first_team"
                        await newsStore.load(1)
                    } else if (!newsStore.items.length) {
                        await newsStore.load(1)
                    }
                    if (!gamesStore.items.length) await gamesStore.load(50)
                    if (!playersStore.items.length) await playersStore.load(200)
                },
            },
            {
                path: 'club',
                component: () => import('@/pages/club/ClubLayout.vue'),
                children: [
                    {
                        path: '',
                        name: 'Club',
                        component: () => import('@/pages/club/ClubIndex.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.heroTitle',
                            heroDescKey: 'pages.clubPage.heroDesc',
                        },
                    },
                    {
                        path: 'general-information',
                        name: 'ClubGeneralInformation',
                        component: () => import('@/pages/club/GeneralInformation.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.cards.general.title',
                            heroDescKey: 'pages.clubPage.cards.general.desc',
                        },
                    },
                    {
                        path: 'club-board',
                        name: 'ClubBoard',
                        component: () => import('@/pages/club/ClubBoard.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.cards.board.title',
                            heroDescKey: 'pages.clubPage.cards.board.desc',
                        },
                    },
                    {
                        path: 'club-history',
                        name: 'ClubHistory',
                        component: () => import('@/pages/club/ClubHistory.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.cards.history.title',
                            heroDescKey: 'pages.clubPage.cards.history.desc',
                        },
                    },
                    {
                        path: 'club-supporters',
                        name: 'ClubSupporters',
                        component: () => import('@/pages/club/ClubSupporters.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.cards.supporters.title',
                            heroDescKey: 'pages.clubPage.cards.supporters.desc',
                        },
                    },
                    {
                        path: 'club-stadium',
                        name: 'ClubStadium',
                        component: () => import('@/pages/club/ClubStadium.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.cards.stadium.title',
                            heroDescKey: 'pages.clubPage.cards.stadium.desc',
                        },
                    },
                    {
                        path: 'club-legends',
                        name: 'ClubLegends',
                        component: () => import('@/pages/club/ClubLegends.vue'),
                        meta: {
                            heroTitleKey: 'pages.clubPage.cards.legends.title',
                            heroDescKey: 'pages.clubPage.cards.legends.desc',
                        },
                    },
                ],
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
                path: "fixtures",
                name: "Fixtures",
                component: () => import("@/pages/Fixtures.vue"),
                beforeEnter: async () => {
                    const gamesStore = useGamesStore()

                    await Promise.all([
                        gamesStore.loadFinished("first_team", 1, 1, "replace"),
                        gamesStore.loadScheduled("first_team"),
                    ])
                },
            },
            {
                path: "youth-team",
                component: () => import("@/pages/youth/YouthLayout.vue"),
                children: [
                    {
                        path: "",
                        name: "YouthTeam",
                        component: () => import("@/pages/youth/YouthIndex.vue"),
                        meta: {
                            heroTitleKey: "pages.youth.heroTitle",
                            heroDescKey: "pages.youth.heroDesc",
                            glassHero: false
                        },
                    },
                    {
                        path: "juniori",
                        name: "JuniorTeam",
                        component: () => import("@/pages/youth/JuniorTeam.vue"),
                        meta: {
                            heroTitleKey: "pages.youth.cards.juniori.title",
                            heroDescKey: "pages.youth.cards.juniori.desc",
                            heroImg: "/club/juniori-hero.jpg",
                            glassHero: true
                        }
                    },
                    {
                        path: "kadeti",
                        name: "KadetiTeam",
                        component: () => import("@/pages/youth/KadetiTeam.vue"),
                        meta: {
                            heroTitleKey: "pages.youth.cards.kadeti.title",
                            heroDescKey: "pages.youth.cards.kadeti.desc",
                            heroImg: "/club/kadeti-hero.jpg",
                            glassHero: true
                        },
                    },
                    {
                        path: "pioniri",
                        name: "PioniriTeam",
                        component: () => import("@/pages/youth/PioniriTeam.vue"),
                        meta: {
                            heroTitleKey: "pages.youth.cards.pioniri.title",
                            heroDescKey: "pages.youth.cards.pioniri.desc",
                            heroImg: "/club/pioniri-hero.jpg",
                            glassHero: true
                        },
                    },
                ],
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
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition
        return {top: 0}
    },
})


export default router
