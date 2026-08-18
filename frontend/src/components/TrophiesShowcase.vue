<template>
  <section class="relative overflow-hidden bg-[#031426] py-16 lg:py-24">
    <!-- Background ambience -->
    <div class="pointer-events-none absolute inset-0">
      <div
          class="absolute inset-0
               bg-[radial-gradient(circle_at_center,rgba(15,61,103,0.32),transparent_58%)]"
      />

      <div
          class="absolute -right-24 top-[-100px]
               h-[620px] w-[620px]
               rounded-full border border-white/[0.025]"
      />

      <div
          class="absolute right-[-20px] top-[-20px]
               h-[460px] w-[460px]
               rounded-full border border-white/[0.02]"
      />
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Heading -->
      <div class="mb-10 text-center lg:mb-14">
        <div class="mb-4 flex items-center justify-center gap-4">
          <span class="h-px w-12 bg-[#D8AD59]/60" />

          <span
              class="text-[11px] font-bold uppercase
                   tracking-[0.35em] text-[#D8AD59]"
          >
            {{ t('trophies.sectionLabel') }}
          </span>

          <span class="h-px w-12 bg-[#D8AD59]/60" />
        </div>

        <h2
            class="text-3xl font-black uppercase tracking-tight
                 text-white md:text-5xl lg:text-6xl"
        >
          {{ t('trophies.title') }}
        </h2>

        <p
            class="mx-auto mt-4 max-w-2xl
                 text-sm leading-6 text-white/50
                 md:text-base"
        >
          {{ t('trophies.description') }}
        </p>
      </div>

      <!-- Carousel -->
      <div
          class="relative mx-auto h-[610px] max-w-6xl
               select-none touch-pan-y
               sm:h-[650px]
               lg:h-[690px]"
          @pointerdown="onPointerDown"
          @pointermove="onPointerMove"
          @pointerup="onPointerUp"
          @pointercancel="onPointerUp"
      >
        <!-- Spotlight beam -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[-90px] z-[1]
                 h-[520px] w-[300px]
                 -translate-x-1/2
                 opacity-90"
        >
          <div
              class="absolute inset-0
                   bg-[linear-gradient(to_bottom,rgba(255,222,150,0.18),rgba(216,173,89,0.07)_45%,transparent_88%)]
                   blur-2xl"
          />

          <div
              class="absolute left-1/2 top-0
                   h-[430px] w-[110px]
                   -translate-x-1/2
                   bg-[linear-gradient(to_bottom,rgba(255,239,198,0.15),transparent)]
                   blur-xl"
          />
        </div>

        <!-- Active radial glow -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[40%] z-[1]
                 h-[430px] w-[430px]
                 -translate-x-1/2 -translate-y-1/2
                 rounded-full
                 bg-[radial-gradient(circle,rgba(216,173,89,0.18)_0%,rgba(216,173,89,0.07)_38%,transparent_72%)]
                 blur-2xl"
        />

        <!-- Ground reflection -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[64%] z-[2]
                 h-[40px] w-[280px]
                 -translate-x-1/2
                 rounded-[50%]
                 bg-[#D8AD59]/20
                 blur-2xl"
        />

        <!-- Outer orbit -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[58%] z-[0]
                 h-[180px] w-[94%]
                 -translate-x-1/2 -translate-y-1/2
                 rounded-[50%]
                 border border-[#D8AD59]/20
                 sm:h-[200px]"
        />

        <!-- Inner orbit -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[58%] z-[0]
                 h-[135px] w-[78%]
                 -translate-x-1/2 -translate-y-1/2
                 rounded-[50%]
                 border border-dashed border-white/[0.06]
                 sm:h-[160px]"
        />

        <!-- Trophy items -->
        <button
            v-for="(trophy, index) in trophies"
            :key="trophy.id"
            type="button"
            data-no-drag
            :aria-label="t(trophy.shortTitleKey)"
            class="absolute left-1/2 top-[38%]
                 flex -translate-x-1/2 -translate-y-1/2
                 flex-col items-center
                 outline-none
                 transition-[transform,opacity,filter]
                 ease-[cubic-bezier(.22,.61,.36,1)]
                 will-change-transform"
            :class="[
            isActive(index)
              ? 'cursor-default'
              : 'cursor-pointer',

            isMobile
              ? 'duration-500'
              : 'duration-700'
          ]"
            :style="getItemStyle(index)"
            @pointerdown.stop
            @click.stop="activate(index)"
        >
          <div
              class="relative flex items-end justify-center
                   transition-all
                   ease-[cubic-bezier(.22,.61,.36,1)]"
              :class="[
              isActive(index)
                ? 'h-[330px] w-[260px] sm:h-[380px] sm:w-[310px] lg:h-[420px] lg:w-[350px]'
                : 'h-[250px] w-[190px] sm:h-[290px] sm:w-[220px] lg:h-[320px] lg:w-[245px]',

              isMobile
                ? 'duration-500'
                : 'duration-700'
            ]"
          >
            <!-- Active glow -->
            <div
                v-if="isActive(index)"
                class="absolute bottom-2 left-1/2
                     h-20 w-48
                     -translate-x-1/2
                     rounded-full
                     bg-[#D8AD59]/25
                     blur-3xl"
            />

            <!-- Trophy image -->
            <img
                :src="trophy.image"
                :alt="t(trophy.shortTitleKey)"
                draggable="false"
                class="relative z-20
                     max-h-full max-w-full
                     object-contain
                     transition-all
                     ease-[cubic-bezier(.22,.61,.36,1)]
                     will-change-transform"
                :class="[
                isActive(index)
                  ? 'trophy-active scale-105 brightness-110 contrast-105 drop-shadow-[0_24px_38px_rgba(0,0,0,0.65)]'
                  : 'scale-90 grayscale brightness-[0.7] contrast-95 opacity-65 drop-shadow-[0_16px_22px_rgba(0,0,0,0.5)]',

                isMobile
                  ? 'duration-500'
                  : 'duration-700'
              ]"
            />

            <!-- Active highlight -->
            <div
                v-if="isActive(index)"
                class="pointer-events-none absolute inset-0 z-30
                     rounded-full
                     bg-[radial-gradient(circle,rgba(255,230,165,0.12),transparent_65%)]
                     mix-blend-screen"
            />
          </div>

          <!-- Side label -->
          <div
              v-if="!isActive(index)"
              class="mt-2 hidden text-center sm:block"
          >
            <p
                class="text-[10px] font-bold uppercase
                     tracking-[0.17em] text-white/45"
            >
              {{ t(trophy.shortTitleKey) }}
            </p>

            <p class="mt-1 text-xs text-[#D8AD59]/70">
              {{ trophy.mainSeason }}
            </p>
          </div>
        </button>

        <!-- Previous -->
        <button
            type="button"
            data-no-drag
            :disabled="isRotating"
            :aria-label="t('trophies.previous')"
            class="absolute left-0 top-[43%] z-[60]
                 flex h-11 w-11 -translate-y-1/2
                 items-center justify-center
                 rounded-full
                 border border-[#D8AD59]/50
                 bg-[#031426]/75
                 text-3xl font-light text-[#E2B75E]
                 backdrop-blur-md
                 transition-all duration-300
                 hover:scale-105
                 hover:border-[#E2B75E]
                 hover:bg-[#09233d]
                 disabled:pointer-events-none
                 disabled:opacity-50
                 sm:h-12 sm:w-12"
            @pointerdown.stop
            @click.stop.prevent="prev"
        >
          ‹
        </button>

        <!-- Next -->
        <button
            type="button"
            data-no-drag
            :disabled="isRotating"
            :aria-label="t('trophies.next')"
            class="absolute right-0 top-[43%] z-[60]
                 flex h-11 w-11 -translate-y-1/2
                 items-center justify-center
                 rounded-full
                 border border-[#D8AD59]/50
                 bg-[#031426]/75
                 text-3xl font-light text-[#E2B75E]
                 backdrop-blur-md
                 transition-all duration-300
                 hover:scale-105
                 hover:border-[#E2B75E]
                 hover:bg-[#09233d]
                 disabled:pointer-events-none
                 disabled:opacity-50
                 sm:h-12 sm:w-12"
            @pointerdown.stop
            @click.stop.prevent="next"
        >
          ›
        </button>

        <!-- Active info -->
        <div
            class="absolute bottom-0 left-1/2 z-50
                 w-full max-w-4xl
                 -translate-x-1/2
                 px-4 text-center"
        >
          <Transition
              name="trophy-info"
              mode="out-in"
          >
            <div :key="activeTrophy.id">
              <div class="mb-3 flex items-center justify-center gap-3">
                <span class="h-px w-10 bg-[#D8AD59]/50" />

                <span
                    class="text-[10px] font-bold uppercase
                         tracking-[0.28em] text-[#D8AD59]"
                >
                  {{ t(activeTrophy.typeKey) }}
                </span>

                <span class="h-px w-10 bg-[#D8AD59]/50" />
              </div>

              <h3
                  class="text-2xl font-black uppercase
                       leading-tight text-white
                       sm:text-3xl lg:text-4xl"
              >
                {{ t(activeTrophy.titleKey) }}
              </h3>

              <div
                  class="mt-5 flex flex-col items-center
                       justify-center gap-4
                       sm:flex-row sm:gap-7"
              >
                <!-- Count -->
                <div class="flex items-center gap-3 sm:block">
                  <p
                      class="text-4xl font-black leading-none
                           text-[#E1B45E]
                           sm:text-5xl"
                  >
                    {{ activeTrophy.count }}x
                  </p>

                  <p
                      class="text-[9px] font-bold uppercase
                           tracking-[0.22em] text-white/35
                           sm:mt-1"
                  >
                    {{ t('trophies.won') }}
                  </p>
                </div>

                <div
                    class="hidden h-12 w-px
                         bg-white/10 sm:block"
                />

                <!-- Seasons -->
                <div
                    class="max-w-[580px]
                         text-center sm:text-left"
                >
                  <p
                      class="text-[9px] font-semibold uppercase
                           tracking-[0.2em] text-white/30"
                  >
                    {{ t('trophies.seasons') }}
                  </p>

                  <p
                      class="mt-1 text-sm leading-6
                           text-white/70
                           sm:text-base"
                  >
                    {{ activeTrophy.seasons }}
                  </p>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>

      <!-- Dots -->
      <div class="mt-8 flex items-center justify-center gap-3">
        <button
            v-for="(_, index) in trophies"
            :key="index"
            type="button"
            data-no-drag
            :disabled="isRotating"
            :aria-label="
            t('trophies.showTrophy', {
              number: index + 1,
            })
          "
            class="h-2.5 rounded-full
                 transition-all duration-300
                 disabled:pointer-events-none"
            :class="
            isActive(index)
              ? 'w-8 bg-[#D8AD59]'
              : 'w-2.5 bg-white/15 hover:bg-white/35'
          "
            @pointerdown.stop
            @click.stop="activate(index)"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import {
  computed,
  onBeforeUnmount,
  onMounted,
  ref,
} from 'vue'

import { useI18n } from 'vue-i18n'

type Trophy = {
  id: number
  count: number
  typeKey: string
  titleKey: string
  shortTitleKey: string
  mainSeason: string
  seasons: string
  image: string
}

type RotationDirection =
    | 'next'
    | 'prev'
    | null

const { t } = useI18n()

const trophies: Trophy[] = [
  {
    id: 1,
    count: 4,
    typeKey: 'trophies.championship',
    titleKey: 'trophies.republikaSrpska',
    shortTitleKey: 'trophies.championshipRs',
    mainSeason: '2023/24',
    seasons:
        '1998/99 · 2004/05 · 2011/12 · 2023/24',
    image: '/trophies/pobjednik-rs-img.png',
  },

  {
    id: 2,
    count: 7,
    typeKey: 'trophies.cup',
    titleKey: 'trophies.republikaSrpska',
    shortTitleKey: 'trophies.cupRs',
    mainSeason: '2018/19',
    seasons:
        '2009/10 · 2012/13 · 2013/14 · 2015/16 · 2016/17 · 2017/18 · 2018/19',
    image: '/trophies/kup-rs-img.png',
  },

  {
    id: 3,
    count: 1,
    typeKey: 'trophies.cup',
    titleKey: 'trophies.bosniaHerzegovina',
    shortTitleKey: 'trophies.cupBih',
    mainSeason: '2015/16',
    seasons: '2015/16',
    image: '/trophies/kup-bih-img.png',
  },
]

/**
 * ------------------------------------------------------
 * Responsive viewport
 * ------------------------------------------------------
 */

const viewportWidth = ref(1280)

const updateViewportWidth = () => {
  if (typeof window === 'undefined') {
    return
  }

  viewportWidth.value = window.innerWidth
}

const isMobile = computed(() => {
  return viewportWidth.value < 640
})

const isTablet = computed(() => {
  return (
      viewportWidth.value >= 640 &&
      viewportWidth.value < 1024
  )
})

/**
 * ------------------------------------------------------
 * Active trophy
 * ------------------------------------------------------
 */

const activeIndex = ref(0)

const activeTrophy = computed<Trophy>(() => {
  return trophies[activeIndex.value] ?? trophies[0]!
})

const isActive = (index: number) => {
  return index === activeIndex.value
}

/**
 * ------------------------------------------------------
 * Mobile rotation state
 * ------------------------------------------------------
 */

const wrappingIndex = ref<number | null>(null)

const wrappingDirection =
    ref<RotationDirection>(null)

const isRotating = ref(false)

let wrapTimer:
    | ReturnType<typeof setTimeout>
    | null = null

let rotationTimer:
    | ReturnType<typeof setTimeout>
    | null = null

/**
 * Faza u kojoj zadnji trofej tone u pozadinu.
 */
const MOBILE_WRAP_PHASE = 260

/**
 * Ukupno trajanje rotacije.
 */
const MOBILE_ROTATION_DURATION = 520

const clearRotationTimers = () => {
  if (wrapTimer) {
    clearTimeout(wrapTimer)
    wrapTimer = null
  }

  if (rotationTimer) {
    clearTimeout(rotationTimer)
    rotationTimer = null
  }
}

/**
 * Nakon 260 ms skidamo posebnu BACK poziciju.
 *
 * Trofej tada već ima opacity 0 pa browser
 * može bez vidljivog "presijecanja" krenuti
 * prema novoj side poziciji.
 */
const finishMobileRotation = () => {
  clearRotationTimers()

  wrapTimer = setTimeout(() => {
    wrappingIndex.value = null
  }, MOBILE_WRAP_PHASE)

  rotationTimer = setTimeout(() => {
    wrappingDirection.value = null
    isRotating.value = false
  }, MOBILE_ROTATION_DURATION)
}

/**
 * ------------------------------------------------------
 * Relative position
 * ------------------------------------------------------
 *
 * -1 = left
 *  0 = center
 *  1 = right
 */

const getRelativePosition = (index: number) => {
  const total = trophies.length

  let diff =
      index - activeIndex.value

  if (diff > total / 2) {
    diff -= total
  }

  if (diff < -total / 2) {
    diff += total
  }

  return diff
}

/**
 * ------------------------------------------------------
 * Transform / positions
 * ------------------------------------------------------
 */

const getItemStyle = (index: number) => {
  const position =
      getRelativePosition(index)

  const mobile = isMobile.value
  const tablet = isTablet.value

  /**
   * Mobile:
   *
   * Bočni pehari su malo bliže centru,
   * spušteniji i manji.
   *
   * Tako centralni trofej djeluje dublje
   * i carousel je vizuelno prirodniji.
   */
  const sideDistance = mobile
      ? 125
      : tablet
          ? 255
          : 360

  const sideScale =
      mobile ? 0.60 : 0.80

  const sideY =
      mobile ? 48 : 55

  /**
   * --------------------------------------------------
   * MOBILE BACK / WRAP
   * --------------------------------------------------
   *
   * Ovdje je najvažnija izmjena.
   *
   * PRETHODNO:
   *
   * side -> translateX(0) -> center/back
   *
   * Zato je izgledalo kao da trofej
   * presijeca centralni dio.
   *
   * SADA:
   *
   * NEXT:
   * left -> left/back -> invisible -> right
   *
   * PREV:
   * right -> right/back -> invisible -> left
   */

  if (
      mobile &&
      wrappingIndex.value === index
  ) {
    const backX =
        wrappingDirection.value === 'next'
            ? -105
            : 105

    return {
      transform: `
        translate(-50%, -50%)
        translateX(${backX}px)
        translateY(95px)
        scale(0.42)
      `,
      opacity: '0',
      zIndex: '5',
      transitionDuration: '260ms',
    }
  }

  /**
   * CENTER
   */
  if (position === 0) {
    return {
      transform: `
        translate(-50%, -50%)
        translateX(0px)
        translateY(-18px)
        scale(1)
      `,
      opacity: '1',
      zIndex: '30',
      transitionDuration:
          mobile
              ? '500ms'
              : '700ms',
    }
  }

  /**
   * LEFT
   */
  if (position === -1) {
    return {
      transform: `
        translate(-50%, -50%)
        translateX(-${sideDistance}px)
        translateY(${sideY}px)
        scale(${sideScale})
      `,
      opacity:
          mobile
              ? '0.52'
              : '0.72',
      zIndex: '20',
      transitionDuration:
          mobile
              ? '500ms'
              : '700ms',
    }
  }

  /**
   * RIGHT
   */
  if (position === 1) {
    return {
      transform: `
        translate(-50%, -50%)
        translateX(${sideDistance}px)
        translateY(${sideY}px)
        scale(${sideScale})
      `,
      opacity:
          mobile
              ? '0.52'
              : '0.72',
      zIndex: '20',
      transitionDuration:
          mobile
              ? '500ms'
              : '700ms',
    }
  }

  /**
   * Hidden fallback
   */
  return {
    transform: `
      translate(-50%, -50%)
      translateY(95px)
      scale(0.42)
    `,
    opacity: '0',
    zIndex: '5',
  }
}

/**
 * ------------------------------------------------------
 * Navigation
 * ------------------------------------------------------
 */

const next = () => {
  if (isRotating.value) {
    return
  }

  /**
   * Desktop/tablet ostaju standardni.
   */
  if (!isMobile.value) {
    activeIndex.value =
        (
            activeIndex.value +
            1
        ) % trophies.length

    return
  }

  isRotating.value = true

  wrappingDirection.value =
      'next'

  /**
   * Kada ide NEXT,
   * trenutni LEFT pehar mora
   * završiti na RIGHT strani.
   */
  wrappingIndex.value =
      (
          activeIndex.value -
          1 +
          trophies.length
      ) % trophies.length

  activeIndex.value =
      (
          activeIndex.value +
          1
      ) % trophies.length

  finishMobileRotation()
}

const prev = () => {
  if (isRotating.value) {
    return
  }

  if (!isMobile.value) {
    activeIndex.value =
        (
            activeIndex.value -
            1 +
            trophies.length
        ) % trophies.length

    return
  }

  isRotating.value = true

  wrappingDirection.value =
      'prev'

  /**
   * Kada ide PREV,
   * trenutni RIGHT pehar mora
   * završiti na LEFT strani.
   */
  wrappingIndex.value =
      (
          activeIndex.value +
          1
      ) % trophies.length

  activeIndex.value =
      (
          activeIndex.value -
          1 +
          trophies.length
      ) % trophies.length

  finishMobileRotation()
}

/**
 * Clicking trophy/dot uses same animation.
 */
const activate = (index: number) => {
  if (
      index < 0 ||
      index >= trophies.length ||
      index === activeIndex.value ||
      isRotating.value
  ) {
    return
  }

  const relative =
      getRelativePosition(index)

  if (relative === 1) {
    next()
    return
  }

  if (relative === -1) {
    prev()
    return
  }

  activeIndex.value = index
}

/**
 * ------------------------------------------------------
 * Swipe / drag
 * ------------------------------------------------------
 */

const isDragging = ref(false)

const startX = ref(0)

const currentX = ref(0)

const dragDistance = ref(0)

const onPointerDown = (
    event: PointerEvent,
) => {
  if (isRotating.value) {
    return
  }

  const target =
      event.target as HTMLElement

  /**
   * Interactive elements don't start drag.
   */
  if (
      target.closest(
          'button, a, input, textarea, select, [data-no-drag]',
      )
  ) {
    return
  }

  isDragging.value = true

  startX.value =
      event.clientX

  currentX.value =
      event.clientX

  dragDistance.value = 0

  const carousel =
      event.currentTarget as HTMLElement

  carousel.setPointerCapture?.(
      event.pointerId,
  )
}

const onPointerMove = (
    event: PointerEvent,
) => {
  if (!isDragging.value) {
    return
  }

  currentX.value =
      event.clientX

  dragDistance.value =
      currentX.value -
      startX.value
}

const onPointerUp = (
    event: PointerEvent,
) => {
  if (!isDragging.value) {
    return
  }

  const threshold =
      isMobile.value
          ? 45
          : 55

  /**
   * Swipe right -> previous.
   */
  if (
      dragDistance.value >
      threshold
  ) {
    prev()
  }

  /**
   * Swipe left -> next.
   */
  else if (
      dragDistance.value <
      -threshold
  ) {
    next()
  }

  const carousel =
      event.currentTarget as HTMLElement

  if (
      carousel.hasPointerCapture?.(
          event.pointerId,
      )
  ) {
    carousel.releasePointerCapture(
        event.pointerId,
    )
  }

  isDragging.value = false

  dragDistance.value = 0
}

/**
 * ------------------------------------------------------
 * Lifecycle
 * ------------------------------------------------------
 */

onMounted(() => {
  updateViewportWidth()

  window.addEventListener(
      'resize',
      updateViewportWidth,
      {
        passive: true,
      },
  )
})

onBeforeUnmount(() => {
  clearRotationTimers()

  window.removeEventListener(
      'resize',
      updateViewportWidth,
  )
})
</script>

<style scoped>
@keyframes trophyReveal {
  0% {
    filter:
        brightness(0.72)
        drop-shadow(
            0 0 0
            rgba(216, 173, 89, 0)
        );
  }

  42% {
    filter:
        brightness(1.25)
        drop-shadow(
            0 0 30px
            rgba(216, 173, 89, 0.35)
        );
  }

  100% {
    filter:
        brightness(1.1)
        drop-shadow(
            0 0 14px
            rgba(216, 173, 89, 0.18)
        );
  }
}

.trophy-active {
  animation:
      trophyReveal
      0.75s
      ease
      forwards;
}

/**
 * Info transition
 */
.trophy-info-enter-active,
.trophy-info-leave-active {
  transition:
      opacity 0.28s ease,
      transform 0.28s ease;
}

.trophy-info-enter-from {
  opacity: 0;
  transform: translateY(12px);
}

.trophy-info-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/**
 * Mobile rendering optimization
 */
@media (max-width: 639px) {
  button[style*='transform'] {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transform-style: preserve-3d;
  }
}

/**
 * Accessibility
 */
@media (prefers-reduced-motion: reduce) {
  .trophy-active {
    animation: none;
  }

  .trophy-info-enter-active,
  .trophy-info-leave-active {
    transition: none;
  }
}
</style>