import type {Router} from "vue-router"

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
    if (clean.toLowerCase().includes(SITE_NAME.toLowerCase())) return clean
    return `${clean} | ${SITE_NAME}`
}

function getLocaleString(i18n: any): string {
    // legacy:false => locale je Ref, ali ako ikad bude string, fallback radi
    const l = i18n?.global?.locale
    if (l && typeof l === "object" && "value" in l) return String(l.value || "sr-Latn")
    return String(l || "sr-Latn")
}

function tString(i18n: any, key: string): string {
    // vue-i18n t() tipovi znaju biti union, pa ovo držimo “runtime-safe”
    const t = i18n?.global?.t
    if (typeof t === "function") return String(t(key))
    return key
}

function resolveRouteText(router: Router, i18n: any) {
    const route = router.currentRoute.value

    const safeT = (key?: string) => {
        if (!key) return ""
        const out = tString(i18n, key)
        if (!out || out === key) return ""
        return out.trim()
    }

    const seoTitleKey = route.meta.seoTitleKey as string | undefined
    const seoDescKey = route.meta.seoDescKey as string | undefined

    const heroTitleKey = route.meta.heroTitleKey as string | undefined
    const heroDescKey = route.meta.heroDescKey as string | undefined

    const rawTitle = seoTitleKey ? safeT(seoTitleKey) : safeT(heroTitleKey)
    const rawDesc = seoDescKey ? safeT(seoDescKey) : safeT(heroDescKey)

    const title = buildTitle(rawTitle || "Zvanična stranica")
    const description =
        rawDesc && rawDesc.length > 10
            ? rawDesc
            : "Zvanična stranica FK Radnik Bijeljina. Vijesti, rezultati, raspored, igrači, tabela i informacije o klubu."

    const url = `${SITE_URL}${route.fullPath}`

    return {title, description, url}
}

export function setupSeo(router: Router, i18n: any) {
    const apply = () => {
        const {title, description, url} = resolveRouteText(router, i18n)

        const locale = getLocaleString(i18n)
        document.documentElement.lang = locale

        document.title = title
        setOrCreateMetaByName("description", description)

        setCanonical(url)

        setOrCreateMetaByProperty("og:type", "website")
        setOrCreateMetaByProperty("og:site_name", SITE_NAME)
        setOrCreateMetaByProperty("og:title", title)
        setOrCreateMetaByProperty("og:description", description)
        setOrCreateMetaByProperty("og:url", url)
        setOrCreateMetaByProperty("og:image", DEFAULT_OG_IMAGE)

        setOrCreateMetaByName("twitter:card", "summary_large_image")
        setOrCreateMetaByName("twitter:title", title)
        setOrCreateMetaByName("twitter:description", description)
        setOrCreateMetaByName("twitter:image", DEFAULT_OG_IMAGE)
    }

    router.afterEach(() => {
        queueMicrotask(apply)
    })

    let lastLocale = getLocaleString(i18n)
    setInterval(() => {
        const current = getLocaleString(i18n)
        if (current !== lastLocale) {
            lastLocale = current
            apply()
        }
    }, 250)

    apply()
}
