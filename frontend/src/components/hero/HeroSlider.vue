<script setup lang="ts">
import {computed, ref} from 'vue'
import {useRouter} from 'vue-router'

type Panel = {
    shortKey: string
    kickerKey: string
    titleKey: string
    descKey: string
    image: string
    ctas: { labelKey: string; to: string }[]
}

const router = useRouter()

const active = ref(0)

const panels: Panel[] = [
    {
        shortKey: 'hero.panels.team.short',
        kickerKey: 'hero.panels.team.kicker',
        titleKey: 'hero.panels.team.title',
        descKey: 'hero.panels.team.desc',
        image: '/hero/fk-radnik-hero-1.jpg',
        ctas: [{labelKey: 'hero.panels.team.cta', to: '/first-team'}],
    },
    {
        shortKey: 'hero.panels.news.short',
        kickerKey: 'hero.panels.news.kicker',
        titleKey: 'hero.panels.news.title',
        descKey: 'hero.panels.news.desc',
        image: '/hero/fk-radnik-joma.jpg',
        ctas: [{labelKey: 'hero.panels.news.cta', to: '/news'}],
    },
    {
        shortKey: 'hero.panels.results.short',
        kickerKey: 'hero.panels.results.kicker',
        titleKey: 'hero.panels.results.title',
        descKey: 'hero.panels.results.desc',
        image: '/hero/fk-radnik-bozo.jpg',
        ctas: [{labelKey: 'hero.panels.results.cta', to: '/fixtures'}],
    },
    {
        shortKey: 'hero.panels.women.short',
        kickerKey: 'hero.panels.women.kicker',
        titleKey: 'hero.panels.women.title',
        descKey: 'hero.panels.women.desc',
        image: '/hero/fk-radnik-zene.webp',
        ctas: [{labelKey: 'hero.panels.women.cta', to: '/women-team'}],
    },
]
const widths = computed(() => {
    const other = 50 / (panels.length - 1)
    return panels.map((_, i) => (i === active.value ? 50 : other))
})

const lefts = computed(() => {
    const arr: number[] = []
    let acc = 0

    for (let i = 0; i < panels.length; i++) {
        arr.push(acc)
        const w = widths.value[i] ?? 0
        acc += w
    }

    return arr
})

const bleedPx = 90
const edgeBleedPx = 8

function setActive(i: number) {
    active.value = i
}

function go(to: string) {
    router.push(to)
}

function panelStyle(i: number) {
    const p = panels[i]
    if (!p) return {} as Record<string, string | number>

    const isFirst = i === 0
    const isLast = i === panels.length - 1

    const w = widths.value[i] ?? 0
    const left = lefts.value[i] ?? 0

    return {
        backgroundImage: `url(${p.image})`,
        zIndex: i === active.value ? 20 : 10 - Math.abs(i - active.value),

        left: isFirst ? `-${edgeBleedPx}px` : isLast ? 'auto' : `${left}%`,
        right: isLast ? `-${edgeBleedPx}px` : 'auto',

        width: `calc(${w}% + ${isLast ? edgeBleedPx : bleedPx}px + ${isFirst ? edgeBleedPx : 0}px)`,
    } as Record<string, string | number>
}

</script>

<template>
    <section class="hero-diagonal">
        <div class="stack">
            <button
                    v-for="(p, i) in panels"
                    :key="i"
                    class="panel"
                    :class="{ active: i === active }"
                    :data-edge="i === 0 ? 'first' : i === panels.length - 1 ? 'last' : 'mid'"
                    type="button"
                    :style="panelStyle(i)"
                    @mouseenter="setActive(i)"
                    @focus="setActive(i)"
                    @click="setActive(i)"
            >
                <div class="overlay"></div>

                <!-- logo samo na aktivnom -->
                <img
                        v-if="i === active"
                        src="/logo/FK_Radnik_logo.png"
                        alt="FK Radnik"
                        class="panel-logo"
                />

                <div v-if="i !== active" class="center-label">
                    {{ $t(p.shortKey) }}
                </div>

                <div v-if="i === active" class="content">
                    <p class="kicker">{{ $t(p.kickerKey) }}</p>
                    <h2 class="title">{{ $t(p.titleKey) }}</h2>
                    <p class="desc">{{ $t(p.descKey) }}</p>

                    <div class="cta-row">
                        <button
                                v-for="(cta, idx) in p.ctas"
                                :key="idx"
                                type="button"
                                class="cta"
                                @click.stop="go(cta.to)"
                        >
                            {{ $t(cta.labelKey) }}
                        </button>
                    </div>
                </div>
            </button>
        </div>
    </section>
</template>
