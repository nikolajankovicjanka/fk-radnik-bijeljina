import {createApp, watch} from 'vue'
import {createPinia} from 'pinia'

import App from './App.vue'
import router from './router'
import {i18n} from '@/i18n'
import {setupSeo} from "@/plugins/seo"

import {useNewsStore} from '@/stores/news'

import './style.css'

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)
app.use(i18n)
setupSeo(router, i18n)

app.mount('#app')

const newsStore = useNewsStore(pinia)

watch(
    () => i18n.global.locale.value,
    () => {
        newsStore.load(1)
    }
)
