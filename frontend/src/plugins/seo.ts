import type {Router} from "vue-router"
import type {I18n} from "vue-i18n"

const SITE_NAME = "FK Radnik Bijeljina"
const SITE_URL = "https://fkradnikbijeljina.com"
const DEFAULT_OG_IMAGE = `${SITE_URL}/logo/FK_Radnik_logo.png`

function setOrCreateMetaByName(name: string, content: string) {
    let el = document.querySelector(`meta[name="${name}"]`) as HTMLMetaElement | null
    if (!el) {
        el = document.createElement("meta")
        el.setAttribute("name", name)
        document.head.appendChild(el)
    }
    el.setAttribute("content", content)
}

function setOrCreateMetaByProperty(property: string, content: string) {
    let el = document.querySelector(`meta[property="${property}"]`) as HTMLMetaElement | null
    if (!el) {
        el = document.createElement("meta")
        el.setAttribute("property", property)
        document.head.appendChild(el)
    }
    el.setAttribute("content", content)
}

function setCanonical(href: string) {
    let el = document.querySelector(`link[rel="canonical"]`) as HTMLLinkElement | null
    if (!el) {
        el = document.createElement("link")
        el.setAttribute("rel", "canonical")
        document.head.appendChild(el)
    }
    el.setAttribute("href", href)
}

function buildTitle(pageTitle: string) {
    const clean = pageTitle?.trim()
    if (!clean) return `${SITE_NAME} | Zvanična stranica`
    // Ako već sadrži ime sajta, ne dupliraj
    if (clean.toLowerCase().includes(SITE_NAME.toLowerCase())) return clean
    return `${clean} | ${SITE_NAME}`
}

function resolveRouteText(router: Router, i18n: I18n) {
    const route = router.currentRoute.value
    const t = i18n.global.t as (key: string) => string

    const safeT = (key?: string) => {
        if (!key) return ""
        const out = t(key)
        // Ako prevod ne postoji, vue-i18n često vrati sam key
        if (!out || out === key) return ""
        return String(out).trim()
    }

    const seoTitleKey = route.meta.seoTitleKey as string | undefined
    const seoDescKey = route.meta.seoDescKey as string | undefined

    const heroTitleKey = route.meta.heroTitleKey as string | undefined
    const heroDescKey = route.meta.heroDescKey as string | undefined

    const rawTitle = seoTitleKey ? safeT(seoTitleKey) : safeT(heroTitleKey)
    const rawDesc = seoDescKey ? safeT(seoDescKey) : safeT(heroDescKey)

    const title = buildTitle(rawTitle || "Zvanična stranica")
    const description =
        (rawDesc && rawDesc.length > 10)
            ? rawDesc
            : "Zvanična stranica FK Radnik Bijeljina. Vijesti, rezultati, raspored, igrači, tabela i informacije o klubu."

    const url = `${SITE_URL}${route.fullPath}`

    return {title, description, url}
}


export function setupSeo(router: Router, i18n: I18n) {
    const apply = () => {
        const {title, description, url} = resolveRouteText(router, i18n)

        // lang attribute (pošto imaš 5 jezika)
        const locale = (i18n.global.locale as unknown as {
                value?: string
            })?.value
            ?? (i18n.global.locale as unknown as string)
            ?? "bs"
        document.documentElement.lang = String(locale)

        // Title + basic meta
        document.title = title
        setOrCreateMetaByName("description", description)

        // Canonical
        setCanonical(url)

        // OG
        setOrCreateMetaByProperty("og:type", "website")
        setOrCreateMetaByProperty("og:site_name", SITE_NAME)
        setOrCreateMetaByProperty("og:title", title)
        setOrCreateMetaByProperty("og:description", description)
        setOrCreateMetaByProperty("og:url", url)
        setOrCreateMetaByProperty("og:image", DEFAULT_OG_IMAGE)

        // Twitter
        setOrCreateMetaByName("twitter:card", "summary_large_image")
        setOrCreateMetaByName("twitter:title", title)
        setOrCreateMetaByName("twitter:description", description)
        setOrCreateMetaByName("twitter:image", DEFAULT_OG_IMAGE)
    }

    // Na svaku promjenu rute
    router.afterEach(() => {
        // čekaj da se Vue render završi da route.fullPath bude finalan
        queueMicrotask(apply)
    })

    // Na promjenu jezika (refresh meta)
    // radi i za ref locale i za string locale
    const anyLocale = i18n.global.locale as any
    if (anyLocale && typeof anyLocale === "object" && "value" in anyLocale) {
        // vue-i18n v9 ref locale
        let old = anyLocale.value
        setInterval(() => {
            if (anyLocale.value !== old) {
                old = anyLocale.value
                apply()
            }
        }, 250)
    }

    // Init
    apply()
}
