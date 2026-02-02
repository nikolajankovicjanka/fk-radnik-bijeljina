import {createI18n} from 'vue-i18n'
import {messages, type SupportedLocale} from '@/translation'

const defaultLocale: SupportedLocale =
    (localStorage.getItem('fk_lang') as SupportedLocale) ?? 'sr-Latn'

export const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: defaultLocale,
    fallbackLocale: 'sr-Latn',
    messages,
})

export function setLocale(locale: SupportedLocale) {
    i18n.global.locale.value = locale
    localStorage.setItem('fk_lang', locale)
}
