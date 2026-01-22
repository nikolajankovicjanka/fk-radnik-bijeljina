<script setup lang="ts">
  import { computed, ref } from 'vue'
  import { useRouter } from 'vue-router'

  type Panel = {
    short: string
    kicker: string
    title: string
    description: string
    image: string
    ctas: { label: string; to: string }[]
  }

  const router = useRouter()

  const active = ref(0)

  const panels: Panel[] = [
    {
      short: 'Tim',
      kicker: 'PRVI TIM',
      title: 'FK RADNIK BIJELJINA',
      description: 'Prvi tim, sastav i informacije.',
      image: '/hero/fk-radnik-hero-1.jpg',
      ctas: [{ label: 'Raspored', to: '/fixtures' }],
    },
    {
      short: 'Vijesti',
      kicker: 'AKTUELNO',
      title: 'VIJESTI',
      description: 'Aktuelnosti iz FK Radnika.',
      image: '/hero/fk-radnik-joma.jpg',
      ctas: [{ label: 'Sve vijesti', to: '/news' }],
    },
    {
      short: 'Rezultati',
      kicker: 'SVE SELEKCIJE',
      title: 'REZULTATI',
      description: 'Rezultati svih selekcija Radnika.',
      image: '/hero/fk-radnik-bozo.jpg',
      ctas: [{ label: 'Fixtures', to: '/fixtures' }],
    },
    {
      short: 'Žene',
      kicker: 'WOMEN',
      title: 'ŽENSKI TIM',
      description: 'Ženski tim FK Radnika.',
      image: '/hero/fk-radnik-zene.jpg',
      ctas: [{ label: 'Ženski tim', to: '/women-team' }],
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
      acc += widths.value[i]
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
    const isFirst = i === 0
    const isLast = i === panels.length - 1

    return {
      backgroundImage: `url(${panels[i].image})`,
      zIndex: i === active.value ? 20 : 10 - Math.abs(i - active.value),

      left: isFirst ? `-${edgeBleedPx}px` : isLast ? 'auto' : `${lefts.value[i]}%`,
      right: isLast ? `-${edgeBleedPx}px` : 'auto',

      width: `calc(${widths.value[i]}% + ${isLast ? edgeBleedPx : bleedPx}px + ${isFirst ? edgeBleedPx : 0}px)`,
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
          {{ p.short }}
        </div>

        <div v-if="i === active" class="content">
          <p class="kicker">{{ p.kicker }}</p>
          <h2 class="title">{{ p.title }}</h2>
          <p class="desc">{{ p.description }}</p>

          <div class="cta-row">
            <button
              v-for="(cta, idx) in p.ctas"
              :key="idx"
              type="button"
              class="cta"
              @click.stop="go(cta.to)"
            >
              {{ cta.label }}
            </button>
          </div>
        </div>
      </button>
    </div>
  </section>
</template>
