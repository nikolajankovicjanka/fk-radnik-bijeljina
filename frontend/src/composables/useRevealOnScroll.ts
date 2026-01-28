import {nextTick, onBeforeUnmount, onMounted, ref, type Ref} from "vue"

type Options = {
    root?: Element | null
    rootMargin?: string
    threshold?: number | number[]
    once?: boolean
    visibleClass?: string
}

/**
 * Usage:
 * const { setRef, refresh } = useRevealOnScroll({ rootMargin: "0px 0px -10% 0px" })
 * <div :ref="setRef" class="reveal">...</div>
 */
export function useRevealOnScroll(options: Options = {}) {
    const els: Ref<HTMLElement[]> = ref([])
    let io: IntersectionObserver | null = null

    const {
        root = null,
        rootMargin = "0px 0px -10% 0px",
        threshold = 0.15,
        once = true,
        visibleClass = "is-visible",
    } = options

    function setRef(el: Element | null) {
        if (!el) return
        const node = el as HTMLElement
        // prevent duplicates (Vue can call ref multiple times)
        if (!els.value.includes(node)) els.value.push(node)
        // if observer already exists (after mount), observe immediately
        if (io) io.observe(node)
    }

    function createObserver() {
        io = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    const target = entry.target as HTMLElement
                    if (!entry.isIntersecting) continue

                    target.classList.add(visibleClass)

                    if (once && io) {
                        io.unobserve(target)
                    }
                }
            },
            {root, rootMargin, threshold}
        )
    }

    async function refresh() {
        // helpful if DOM changes after async load
        await nextTick()
        if (!io) return
        els.value.forEach((el) => io!.observe(el))
    }

    onMounted(async () => {
        await nextTick()
        createObserver()
        els.value.forEach((el) => io!.observe(el))
    })

    onBeforeUnmount(() => {
        io?.disconnect()
        io = null
        els.value = []
    })

    return {setRef, refresh}
}
