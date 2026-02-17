import {createI18n} from "vue-i18n"
import {messages, type SupportedLocale} from "@/translation"

const FALLBACK_LOCALE: SupportedLocale = "sr-Latn"

function readSavedLocale(): SupportedLocale {
    const raw = localStorage.getItem("fk_lang")
    return raw && raw in messages ? (raw as SupportedLocale) : FALLBACK_LOCALE
}

type MessageSchema = (typeof messages)[typeof FALLBACK_LOCALE]

export const i18n = createI18n<[MessageSchema], SupportedLocale, false>({
    legacy: false,
    globalInjection: true,
    locale: readSavedLocale(),
    fallbackLocale: FALLBACK_LOCALE,
    messages,
})

export function setLocale(locale: SupportedLocale) {
    i18n.global.locale.value = locale
    localStorage.setItem("fk_lang", locale)
}
