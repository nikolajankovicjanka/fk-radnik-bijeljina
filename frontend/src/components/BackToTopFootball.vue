<template>
  <Teleport to="body">
    <div
      class="football-back-top"
      :class="{ 'is-visible': visible || playing }"
      :style="{ '--football-viewport-bottom': viewportBottom }"
    >
      <!-- Nova realistična lopta: bez okvira, pozadine ili zelenog prstena. -->
      <button
        v-if="!playing"
        type="button"
        class="football-back-top__button"
        :disabled="!visible"
        aria-label="Vrati se na vrh stranice"
        title="Nazad na vrh"
        @click="kickToTop"
      >
        <img :src="footballImage" alt="" draggable="false" />
      </button>

      <!-- Trčanje i priprema ostaju iz postojećeg Lottie JSON-a. -->
      <div
        v-show="playing && !ballFlying"
        ref="animationContainer"
        class="football-back-top__animation"
        aria-hidden="true"
      />

      <!-- Završna poza ostaje na mjestu dok se stranica pomjera. -->
      <img
        v-show="playing && ballFlying"
        :src="kickPose"
        class="football-back-top__kick-pose"
        alt=""
        draggable="false"
        aria-hidden="true"
      />
    </div>

    <!-- Let je odvojen od fiksiranog okvira dugmeta i igrača. -->
    <div
      v-if="playing"
      class="football-back-top__flying-ball"
      :class="{ 'is-flying': ballFlying }"
      :style="{ '--football-viewport-bottom': viewportBottom }"
      aria-hidden="true"
    >
      <span class="football-back-top__trail football-back-top__trail--one" />
      <span class="football-back-top__trail football-back-top__trail--two" />
      <span class="football-back-top__trail football-back-top__trail--three" />
      <img :src="footballImage" class="football-back-top__ball-art" alt="" draggable="false" />
    </div>
  </Teleport>
</template>

<script setup lang="ts">
  import { onBeforeUnmount, onMounted, ref } from 'vue'
  import lottie, { type AnimationItem } from 'lottie-web'
  import animationData from '@/assets/animations/radnik-back-to-top.json'
  import kickPose from '@/assets/animations/radnik_sut_lijevom.webp'
  import footballImage from '@/assets/animations/radnik_football_3d.png'

  const visible = ref(false)
  const playing = ref(false)
  const ballFlying = ref(false)
  const viewportBottom = ref('0px')
  const animationContainer = ref<HTMLElement | null>(null)

  const SCROLL_THRESHOLD = 250
  const MOBILE_HIDE_THRESHOLD = 100
  const KICK_MOMENT_MS = 820
  const BALL_FLIGHT_MS = 1200

  let animation: AnimationItem | null = null
  let mobileLayout: MediaQueryList | null = null
  let scrollRaf = 0
  let kickTimer: ReturnType<typeof setTimeout> | undefined

  function updateVisibility(): void {
    // Na telefonu niži prag za skrivanje sprečava treperenje pri promjeni smjera.
    const threshold =
      mobileLayout?.matches && visible.value ? MOBILE_HIDE_THRESHOLD : SCROLL_THRESHOLD
    visible.value = window.scrollY > threshold
  }

  function updateViewportBottom(): void {
    const viewport = window.visualViewport
    if (!viewport) return

    // Razlika između donje ivice fiksnog položaja i vidljivog dijela ekrana.
    const inset = window.innerHeight - viewport.height - viewport.offsetTop
    viewportBottom.value = `${Math.max(0, inset)}px`
  }

  function clearPending(): void {
    clearTimeout(kickTimer)
    cancelAnimationFrame(scrollRaf)
  }

  function animateScrollToTop(duration: number): void {
    const startY = window.scrollY
    const started = performance.now()

    const step = (now: number): void => {
      const t = Math.min((now - started) / duration, 1)
      const easeOutCubic = 1 - Math.pow(1 - t, 3)
      window.scrollTo({ top: Math.round(startY * (1 - easeOutCubic)), behavior: 'instant' })
      if (t < 1) {
        scrollRaf = requestAnimationFrame(step)
        return
      }

      playing.value = false
      ballFlying.value = false
      animation?.stop()
      updateVisibility()
    }

    scrollRaf = requestAnimationFrame(step)
  }

  function kickToTop(): void {
    if (playing.value) return

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !animation) {
      window.scrollTo({ top: 0, behavior: 'instant' })
      return
    }

    clearPending()
    playing.value = true
    ballFlying.value = false

    animation.stop()
    animation.goToAndPlay(0, true)

    // Završna poza i polijetanje lopte počinju u istom kadru.
    kickTimer = setTimeout(() => {
      animation?.pause()
      ballFlying.value = true
      animateScrollToTop(BALL_FLIGHT_MS)
    }, KICK_MOMENT_MS)
  }

  onMounted(() => {
    mobileLayout = window.matchMedia('(max-width: 640px)')
    updateVisibility()
    updateViewportBottom()
    window.addEventListener('scroll', updateVisibility, { passive: true })
    window.addEventListener('resize', updateViewportBottom)
    window.visualViewport?.addEventListener('resize', updateViewportBottom)
    window.visualViewport?.addEventListener('scroll', updateViewportBottom, { passive: true })
    mobileLayout.addEventListener('change', updateVisibility)

    if (!animationContainer.value) return

    // Stari PNG šuta je odrezan, a stara lopta se ne smije duplirati.
    const scene: { layers: Array<{ nm?: string }> } = JSON.parse(JSON.stringify(animationData))
    scene.layers = scene.layers.filter(
      layer => layer.nm !== 'radnik_lopta_let' && layer.nm !== 'radnik_sut'
    )

    animation = lottie.loadAnimation({
      container: animationContainer.value,
      renderer: 'svg',
      loop: false,
      autoplay: false,
      animationData: scene,
      rendererSettings: { preserveAspectRatio: 'xMidYMid meet' },
    })
  })

  onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateVisibility)
    window.removeEventListener('resize', updateViewportBottom)
    window.visualViewport?.removeEventListener('resize', updateViewportBottom)
    window.visualViewport?.removeEventListener('scroll', updateViewportBottom)
    mobileLayout?.removeEventListener('change', updateVisibility)
    clearPending()
    animation?.destroy()
    animation = null
  })
</script>

<style scoped>
  .football-back-top {
    position: fixed;
    right: 20px;
    bottom: 22px;
    z-index: 9999;
    width: 58px;
    height: 58px;
    opacity: 0;
    transform: translateY(18px);
    pointer-events: none;
    transition:
      opacity 220ms ease,
      transform 220ms ease;
  }
  .football-back-top.is-visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }
  .football-back-top__button {
    display: block;
    width: 58px;
    height: 58px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: transparent;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    filter: drop-shadow(0 5px 8px rgb(0 0 0 / 30%));
    transition:
      transform 180ms ease,
      filter 180ms ease;
  }
  .football-back-top__button img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    user-select: none;
  }
  @media (min-width: 641px) and (hover: hover) {
    .football-back-top__button:hover {
      transform: rotate(-12deg) scale(1.12);
      filter: drop-shadow(0 8px 12px rgb(0 0 0 / 36%));
    }
  }
  .football-back-top__button:focus-visible {
    outline: 3px solid #3b82f6;
    outline-offset: 4px;
  }
  .football-back-top__animation {
    position: absolute;
    width: 240px;
    height: 240px;
    right: 12px;
    bottom: -20px;
    pointer-events: none;
  }
  .football-back-top__kick-pose {
    position: absolute;
    width: 240px;
    height: 240px;
    /* Globalni img max-width ne smije suziti pozu na širinu dugmeta. */
    max-width: none;
    right: 12px;
    bottom: 0;
    object-fit: contain;
    object-position: center bottom;
    pointer-events: none;
  }

  /* Teleportovana lopta je fiksirana direktno na body. */
  .football-back-top__flying-ball {
    position: fixed;
    right: 25px;
    bottom: 27px;
    width: 48px;
    height: 48px;
    z-index: 10001;
    pointer-events: none;
    transform-origin: center;
    filter: drop-shadow(0 3px 7px rgb(0 0 0 / 30%));
  }
  .football-back-top__flying-ball.is-flying {
    animation: radnik-ball-flight 1200ms cubic-bezier(0.22, 0.05, 0.45, 1) both;
  }
  .football-back-top__ball-art {
    position: relative;
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .football-back-top__flying-ball.is-flying .football-back-top__ball-art {
    animation: radnik-ball-spin 1200ms ease-in both;
  }
  .football-back-top__trail {
    position: absolute;
    left: 50%;
    top: 75%;
    width: 5px;
    height: 110px;
    border-radius: 100%;
    transform-origin: top center;
    background: linear-gradient(to bottom, rgb(75 167 255 / 75%), transparent);
    filter: blur(3px);
    opacity: 0;
  }
  .football-back-top__flying-ball.is-flying .football-back-top__trail {
    animation: radnik-trail-pulse 260ms ease-in-out infinite alternate;
  }
  .football-back-top__trail--one {
    transform: translateX(-14px) rotate(9deg);
  }
  .football-back-top__trail--two {
    transform: translateX(4px) rotate(-7deg);
    height: 135px;
    animation-delay: 80ms !important;
  }
  .football-back-top__trail--three {
    transform: translateX(16px) rotate(-13deg);
    height: 90px;
    animation-delay: 150ms !important;
  }
  @keyframes radnik-ball-flight {
    0% {
      transform: translate3d(0, 0, 0) scale(1);
      opacity: 1;
      animation-timing-function: ease-out;
    }
    9% {
      transform: translate3d(-2px, -24px, 0) scale(1.04);
      opacity: 1;
    }
    30% {
      transform: translate3d(-10px, -27vh, 0) scale(0.93);
      opacity: 1;
    }
    70% {
      transform: translate3d(5px, -85vh, 0) scale(0.62);
      opacity: 1;
    }
    100% {
      transform: translate3d(0, -130vh, 0) scale(0.3);
      opacity: 0;
    }
  }
  @keyframes radnik-ball-spin {
    from {
      transform: rotate(0);
    }
    to {
      transform: rotate(900deg);
    }
  }
  @keyframes radnik-trail-pulse {
    from {
      opacity: 0.35;
    }
    to {
      opacity: 0.75;
    }
  }

  @media (max-width: 640px) {
    .football-back-top {
      right: 12px;
      bottom: calc(16px + var(--football-viewport-bottom, 0px));
      width: 50px;
      height: 50px;
      transition: opacity 220ms ease;
    }
    .football-back-top,
    .football-back-top.is-visible {
      transform: none;
    }
    .football-back-top__button {
      width: 50px;
      height: 50px;
    }
    .football-back-top__animation {
      width: 190px;
      height: 190px;
      right: 2px;
      bottom: -15px;
    }
    .football-back-top__kick-pose {
      width: 190px;
      height: 190px;
      right: 2px;
    }
    .football-back-top__flying-ball {
      right: 17px;
      bottom: calc(21px + var(--football-viewport-bottom, 0px));
      width: 40px;
      height: 40px;
    }
  }
  @media (prefers-reduced-motion: reduce) {
    .football-back-top,
    .football-back-top__button {
      transition: none;
    }
    .football-back-top__button:hover {
      transform: none;
    }
  }
</style>
