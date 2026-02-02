import {createApp, watch} from 'vue'
import {createPinia} from 'pinia'

import App from './App.vue'
import router from './router'
import {i18n} from '@/i18n'

import {useNewsStore} from '@/stores/news'
import {useGamesStore} from '@/stores/games'
import {usePlayersStore} from '@/stores/players'

import './style.css'

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)
app.use(i18n)

app.mount('#app')

const newsStore = useNewsStore(pinia)
const gamesStore = useGamesStore(pinia)
const playersStore = usePlayersStore(pinia)

watch(
    () => i18n.global.locale.value,
    () => {
        newsStore.load(1)
    }
)
