<template>
  <section
      class="relative overflow-hidden
           bg-[linear-gradient(135deg,#EEF4F8_0%,#DCE8F2_50%,#F4F7F9_100%)]
           py-16 lg:py-24"
  >
    <!-- =====================================================
         BACKGROUND AMBIENCE
    ====================================================== -->
    <div class="pointer-events-none absolute inset-0">
      <!-- Central blue ambience -->
      <div
          class="absolute inset-0
               bg-[radial-gradient(circle_at_center,rgba(3,20,38,0.07),transparent_58%)]"
      />

      <!-- Gold ambience -->
      <div
          class="absolute left-1/2 top-[30%]
               h-[520px] w-[520px]
               -translate-x-1/2
               rounded-full
               bg-[radial-gradient(circle,rgba(216,173,89,0.12),transparent_68%)]
               blur-3xl"
      />

      <!-- Decorative circles -->
      <div
          class="absolute -right-24 top-[-100px]
               h-[620px] w-[620px]
               rounded-full
               border border-[#031426]/[0.04]"
      />

      <div
          class="absolute right-[-20px] top-[-20px]
               h-[460px] w-[460px]
               rounded-full
               border border-[#031426]/[0.035]"
      />

      <div
          class="absolute -left-[180px] bottom-[-260px]
               h-[600px] w-[600px]
               rounded-full
               border border-[#031426]/[0.025]"
      />
    </div>

    <div
        class="relative mx-auto max-w-7xl
             px-4 sm:px-6 lg:px-8"
    >
      <!-- =====================================================
           HEADING
      ====================================================== -->
      <div class="mb-10 text-center lg:mb-14">
        <div
            class="mb-4 flex items-center
                 justify-center gap-4"
        >
          <span
              class="h-px w-12
                   bg-[#D8AD59]/70"
          />

          <span
              class="text-[11px]
                   font-bold uppercase
                   tracking-[0.35em]
                   text-[#B88A36]"
          >
            {{ t('trophies.sectionLabel') }}
          </span>

          <span
              class="h-px w-12
                   bg-[#D8AD59]/70"
          />
        </div>

        <h2
            class="text-3xl font-black uppercase
                 tracking-tight text-[#031426]
                 md:text-5xl lg:text-6xl"
        >
          {{ t('trophies.title') }}
        </h2>

      </div>

      <!-- =====================================================
           CAROUSEL
      ====================================================== -->
      <div
          class="relative mx-auto
               h-[610px] max-w-6xl
               select-none touch-pan-y
               sm:h-[650px]
               lg:h-[690px]"
          @pointerdown="onPointerDown"
          @pointermove="onPointerMove"
          @pointerup="onPointerUp"
          @pointercancel="onPointerUp"
      >
        <!-- ===================================================
             SPOTLIGHT BEAM
        ==================================================== -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[-90px] z-[1]
                 h-[520px] w-[300px]
                 -translate-x-1/2
                 opacity-80"
        >
          <div
              class="absolute inset-0
                   bg-[linear-gradient(to_bottom,rgba(255,218,135,0.24),rgba(216,173,89,0.08)_45%,transparent_88%)]
                   blur-2xl"
          />

          <div
              class="absolute left-1/2 top-0
                   h-[430px] w-[110px]
                   -translate-x-1/2
                   bg-[linear-gradient(to_bottom,rgba(255,226,160,0.2),transparent)]
                   blur-xl"
          />
        </div>

        <!-- ===================================================
             ACTIVE RADIAL GLOW
        ==================================================== -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[40%] z-[1]
                 h-[430px] w-[430px]
                 -translate-x-1/2
                 -translate-y-1/2
                 rounded-full
                 bg-[radial-gradient(circle,rgba(216,173,89,0.16)_0%,rgba(216,173,89,0.07)_38%,transparent_72%)]
                 blur-2xl"
        />

        <!-- ===================================================
             GROUND REFLECTION
        ==================================================== -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[64%] z-[2]
                 h-[38px] w-[280px]
                 -translate-x-1/2
                 rounded-[50%]
                 bg-[#D8AD59]/20
                 blur-2xl"
        />

        <!-- ===================================================
             OUTER ORBIT
        ==================================================== -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[58%] z-[0]
                 h-[180px] w-[94%]
                 -translate-x-1/2
                 -translate-y-1/2
                 rounded-[50%]
                 border border-[#031426]/10
                 sm:h-[200px]"
        />

        <!-- ===================================================
             INNER ORBIT
        ==================================================== -->
        <div
            class="pointer-events-none absolute
                 left-1/2 top-[58%] z-[0]
                 h-[135px] w-[78%]
                 -translate-x-1/2
                 -translate-y-1/2
                 rounded-[50%]
                 border border-dashed
                 border-[#031426]/10
                 sm:h-[160px]"
        />

        <!-- ===================================================
             TROPHY ITEMS
        ==================================================== -->
        <button
            v-for="(trophy, index) in trophies"
            :key="trophy.id"
            type="button"
            data-no-drag
            :aria-label="t(trophy.shortTitleKey)"
            class="trophy-item
                 absolute left-1/2 top-[38%]
                 flex flex-col items-center
                 border-0 bg-transparent p-0
                 outline-none"
            :class="
            isActive(index)
              ? 'cursor-default'
              : 'cursor-pointer'
          "
            :style="getItemStyle(index)"
            @pointerdown.stop
            @click.stop="activate(index)"
        >
          <!--
            VAŽNO:

            Svi trophy containeri imaju ISTU veličinu.

            Ne mijenjamo više width/height kada
            trofej postane aktivan.

            Cijelu promjenu veličine kontroliše
            scale() na trophy-item wrapperu.
          -->
          <div
              class="relative flex
                   h-[330px] w-[260px]
                   items-end justify-center
                   sm:h-[380px] sm:w-[310px]
                   lg:h-[420px] lg:w-[350px]"
          >
            <!-- =================================================
                 ACTIVE GLOW
            ================================================== -->
            <div
                v-if="
                isActive(index) &&
                (!isIOS || !isRotating)
              "
                class="pointer-events-none
                     absolute bottom-2 left-1/2
                     h-20 w-48
                     -translate-x-1/2
                     rounded-full
                     bg-[#D8AD59]/25
                     blur-3xl"
            />

            <!-- =================================================
                 TROPHY IMAGE
            ================================================== -->
            <img
                :src="trophy.image"
                :alt="t(trophy.shortTitleKey)"
                draggable="false"
                class="trophy-image
                     relative z-20
                     max-h-full max-w-full
                     object-contain"
                :class="
                getTrophyImageClass(index)
              "
            />

            <!-- =================================================
                 ACTIVE HIGHLIGHT
            ================================================== -->
            <div
                v-if="
                isActive(index) &&
                (!isIOS || !isRotating)
              "
                class="pointer-events-none
                     absolute inset-0 z-30
                     rounded-full
                     bg-[radial-gradient(circle,rgba(255,230,165,0.16),transparent_65%)]
                     mix-blend-screen"
            />
          </div>

          <!-- =================================================
               SIDE TROPHY LABEL
          ================================================== -->
          <div
              v-if="!isActive(index)"
              class="mt-2 hidden text-center
                   sm:block"
          >
            <p
                class="text-[10px]
                     font-bold uppercase
                     tracking-[0.17em]
                     text-[#031426]/50"
            >
              {{ t(trophy.shortTitleKey) }}
            </p>

            <p
                class="mt-1 text-xs
                     font-medium
                     text-[#B88A36]/80"
            >
              {{ trophy.mainSeason }}
            </p>
          </div>
        </button>

        <!-- ===================================================
             PREVIOUS BUTTON
        ==================================================== -->
        <button
            type="button"
            data-no-drag
            :disabled="isRotating"
            :aria-label="t('trophies.previous')"
            class="absolute left-0 top-[43%]
                 z-[60]
                 flex h-11 w-11
                 -translate-y-1/2
                 items-center justify-center
                 rounded-full
                 border border-[#D8AD59]/70
                 bg-white/75
                 text-3xl font-light
                 text-[#B88A36]
                 shadow-[0_8px_24px_rgba(3,20,38,0.08)]
                 backdrop-blur-md
                 transition-all duration-300
                 hover:scale-105
                 hover:border-[#B88A36]
                 hover:bg-white
                 disabled:pointer-events-none
                 disabled:opacity-50
                 sm:h-12 sm:w-12"
            @pointerdown.stop
            @click.stop.prevent="prev"
        >
          ‹
        </button>

        <!-- ===================================================
             NEXT BUTTON
        ==================================================== -->
        <button
            type="button"
            data-no-drag
            :disabled="isRotating"
            :aria-label="t('trophies.next')"
            class="absolute right-0 top-[43%]
                 z-[60]
                 flex h-11 w-11
                 -translate-y-1/2
                 items-center justify-center
                 rounded-full
                 border border-[#D8AD59]/70
                 bg-white/75
                 text-3xl font-light
                 text-[#B88A36]
                 shadow-[0_8px_24px_rgba(3,20,38,0.08)]
                 backdrop-blur-md
                 transition-all duration-300
                 hover:scale-105
                 hover:border-[#B88A36]
                 hover:bg-white
                 disabled:pointer-events-none
                 disabled:opacity-50
                 sm:h-12 sm:w-12"
            @pointerdown.stop
            @click.stop.prevent="next"
        >
          ›
        </button>

        <!-- ===================================================
             ACTIVE TROPHY INFO
        ==================================================== -->
        <div
            class="absolute bottom-0 left-1/2
                 z-50 w-full max-w-4xl
                 -translate-x-1/2
                 px-4 text-center"
        >
          <Transition
              name="trophy-info"
              mode="out-in"
          >
            <div :key="activeTrophy.id">
              <!-- Competition type -->
              <div
                  class="mb-3 flex items-center
                       justify-center gap-3"
              >
                <span
                    class="h-px w-10
                         bg-[#D8AD59]/70"
                />

                <span
                    class="text-[10px]
                         font-bold uppercase
                         tracking-[0.28em]
                         text-[#B88A36]"
                >
                  {{ t(activeTrophy.typeKey) }}
                </span>

                <span
                    class="h-px w-10
                         bg-[#D8AD59]/70"
                />
              </div>

              <!-- Competition name -->
              <h3
                  class="text-2xl font-black
                       uppercase leading-tight
                       text-[#031426]
                       sm:text-3xl
                       lg:text-4xl"
              >
                {{ t(activeTrophy.titleKey) }}
              </h3>

              <div
                  class="mt-5 flex flex-col
                       items-center justify-center
                       gap-4
                       sm:flex-row sm:gap-7"
              >
                <!-- Count -->
                <div
                    class="flex items-center
                         gap-3 sm:block"
                >
                  <p
                      class="text-4xl font-black
                           leading-none
                           text-[#B88A36]
                           sm:text-5xl"
                  >
                    {{ activeTrophy.count }}x
                  </p>

                  <p
                      class="text-[9px]
                           font-bold uppercase
                           tracking-[0.22em]
                           text-[#031426]/45
                           sm:mt-1"
                  >
                    {{ t('trophies.won') }}
                  </p>
                </div>

                <div
                    class="hidden h-12 w-px
                         bg-[#031426]/15
                         sm:block"
                />

                <!-- Seasons -->
                <div
                    class="max-w-[580px]
                         text-center
                         sm:text-left"
                >
                  <p
                      class="text-[9px]
                           font-semibold uppercase
                           tracking-[0.2em]
                           text-[#031426]/40"
                  >
                    {{ t('trophies.seasons') }}
                  </p>

                  <p
                      class="mt-1 text-sm
                           leading-6
                           text-[#25455F]/80
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

      <!-- =====================================================
           NAVIGATION DOTS
      ====================================================== -->
      <div
          class="mt-8 flex items-center
               justify-center gap-3"
      >
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
              : 'w-2.5 bg-[#031426]/15 hover:bg-[#031426]/30'
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
  type CSSProperties,
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

type WrapPhase =
    | 'idle'
    | 'hide'
    | 'teleport'
    | 'show'

const { t } = useI18n()

/**
 * ============================================================
 * TROPHY DATA
 * ============================================================
 */
const trophies: Trophy[] = [
  {
    id: 1,
    count: 4,
    typeKey:
        'trophies.championship',
    titleKey:
        'trophies.republikaSrpska',
    shortTitleKey:
        'trophies.championshipRs',
    mainSeason:
        '2023/24',
    seasons:
        '1998/99 · 2004/05 · 2011/12 · 2023/24',
    image:
        '/trophies/pobjednik-rs-img.png',
  },

  {
    id: 2,
    count: 7,
    typeKey:
        'trophies.cup',
    titleKey:
        'trophies.republikaSrpska',
    shortTitleKey:
        'trophies.cupRs',
    mainSeason:
        '2018/19',
    seasons:
        '2009/10 · 2012/13 · 2013/14 · 2015/16 · 2016/17 · 2017/18 · 2018/19',
    image:
        '/trophies/kup-rs-img.png',
  },

  {
    id: 3,
    count: 1,
    typeKey:
        'trophies.cup',
    titleKey:
        'trophies.bosniaHerzegovina',
    shortTitleKey:
        'trophies.cupBih',
    mainSeason:
        '2015/16',
    seasons:
        '2015/16',
    image:
        '/trophies/kup-bih-img.png',
  },
]

/**
 * ============================================================
 * DEVICE / VIEWPORT
 * ============================================================
 */
const viewportWidth =
    ref(1280)

const isIOS =
    ref(false)

const detectIOS = () => {
  if (
      typeof navigator ===
      'undefined'
  ) {
    return
  }

  const userAgent =
      navigator.userAgent

  const platform =
      navigator.platform

  const touchPoints =
      navigator.maxTouchPoints ?? 0

  isIOS.value =
      /iPhone|iPad|iPod/i.test(
          userAgent,
      ) ||
      (
          platform === 'MacIntel' &&
          touchPoints > 1
      )
}

const updateViewportWidth = () => {
  if (
      typeof window ===
      'undefined'
  ) {
    return
  }

  viewportWidth.value =
      window.innerWidth
}

const isMobile =
    computed(() => {
      return (
          viewportWidth.value < 640
      )
    })

const isTablet =
    computed(() => {
      return (
          viewportWidth.value >= 640 &&
          viewportWidth.value < 1024
      )
    })

/**
 * ============================================================
 * ACTIVE TROPHY
 * ============================================================
 */
const activeIndex =
    ref(0)

const activeTrophy =
    computed<Trophy>(() => {
      return (
          trophies[
              activeIndex.value
              ] ??
          trophies[0]!
      )
    })

const isActive = (
    index: number,
) => {
  return (
      index ===
      activeIndex.value
  )
}

/**
 * ============================================================
 * ROTATION STATE
 * ============================================================
 */
const isRotating =
    ref(false)

const wrappingIndex =
    ref<number | null>(null)

const rotationDirection =
    ref<RotationDirection>(
        null,
    )

const wrapPhase =
    ref<WrapPhase>('idle')

let timers:
    ReturnType<
        typeof setTimeout
    >[] = []

/**
 * Center <-> side movement.
 */
const MOBILE_MOVE_DURATION =
    480

/**
 * Side trophy fades backwards.
 */
const WRAP_HIDE_DURATION =
    160

/**
 * Short pause after Safari teleport.
 */
const TELEPORT_SETTLE =
    40

/**
 * Wrapped trophy appears
 * on opposite side.
 */
const WRAP_SHOW_DURATION =
    220

/**
 * Total mobile lock.
 */
const MOBILE_ROTATION_DURATION =
    520

const addTimer = (
    callback: () => void,
    delay: number,
) => {
  const timer =
      setTimeout(
          callback,
          delay,
      )

  timers.push(timer)
}

const clearTimers = () => {
  timers.forEach(
      (timer) => {
        clearTimeout(timer)
      },
  )

  timers = []
}

/**
 * ============================================================
 * RELATIVE POSITION
 * ============================================================
 *
 * -1 = left
 *  0 = center
 *  1 = right
 */
const getRelativePosition = (
    index: number,
) => {
  const total =
      trophies.length

  let diff =
      index -
      activeIndex.value

  if (
      diff >
      total / 2
  ) {
    diff -= total
  }

  if (
      diff <
      -total / 2
  ) {
    diff += total
  }

  return diff
}

/**
 * ============================================================
 * POSITION HELPERS
 * ============================================================
 */
const getSideDistance = () => {
  if (
      isMobile.value
  ) {
    return 125
  }

  if (
      isTablet.value
  ) {
    return 255
  }

  return 360
}

const getSideScale = () => {
  if (
      isMobile.value
  ) {
    return 0.60
  }

  return 0.80
}

const getSideY = () => {
  if (
      isMobile.value
  ) {
    return 48
  }

  return 55
}

/**
 * ============================================================
 * TRANSFORM
 * ============================================================
 *
 * Koristimo samo jedan transform
 * na trophy wrapperu.
 *
 * translate3d pomaže WebKit compositingu.
 */
const createTransform = (
    x: number,
    y: number,
    scale: number,
) => {
  return `
    translate3d(-50%, -50%, 0)
    translate3d(${x}px, ${y}px, 0)
    scale(${scale})
  `
}

/**
 * ============================================================
 * ITEM STYLE
 * ============================================================
 */
const getItemStyle = (
    index: number,
): CSSProperties => {
  const position =
      getRelativePosition(
          index,
      )

  const sideDistance =
      getSideDistance()

  const sideScale =
      getSideScale()

  const sideY =
      getSideY()

  /**
   * ----------------------------------------------------------
   * MOBILE WRAP
   * ----------------------------------------------------------
   *
   * NEXT:
   *
   * LEFT
   * ↓
   * hide
   * ↓
   * teleport while invisible
   * ↓
   * RIGHT fade-in
   *
   * PREV radi obrnuto.
   */
  if (
      isMobile.value &&
      wrappingIndex.value ===
      index
  ) {
    const oldSideX =
        rotationDirection.value ===
        'next'
            ? -sideDistance
            : sideDistance

    const newSideX =
        rotationDirection.value ===
        'next'
            ? sideDistance
            : -sideDistance

    /**
     * HIDE
     */
    if (
        wrapPhase.value ===
        'hide'
    ) {
      return {
        transform:
            createTransform(
                oldSideX,
                sideY + 32,
                0.46,
            ),

        opacity:
            '0',

        zIndex:
            '5',

        transitionProperty:
            'transform, opacity',

        transitionDuration:
            `${WRAP_HIDE_DURATION}ms`,

        transitionTimingFunction:
            'ease-out',

        willChange:
            'transform, opacity',
      }
    }

    /**
     * TELEPORT
     *
     * Element je opacity 0,
     * a transform dobije transition:none.
     *
     * Safari zato ne može da ga
     * animira preko centra.
     */
    if (
        wrapPhase.value ===
        'teleport'
    ) {
      return {
        transform:
            createTransform(
                newSideX,
                sideY,
                sideScale,
            ),

        opacity:
            '0',

        zIndex:
            '5',

        transition:
            'none',

        willChange:
            'transform, opacity',
      }
    }

    /**
     * SHOW
     *
     * Element je već fizički
     * na novoj strani.
     *
     * Animiramo samo opacity.
     */
    if (
        wrapPhase.value ===
        'show'
    ) {
      return {
        transform:
            createTransform(
                newSideX,
                sideY,
                sideScale,
            ),

        opacity:
            '0.52',

        zIndex:
            '20',

        transitionProperty:
            'opacity',

        transitionDuration:
            `${WRAP_SHOW_DURATION}ms`,

        transitionTimingFunction:
            'ease-out',

        willChange:
            'opacity',
      }
    }
  }

  /**
   * ----------------------------------------------------------
   * CENTER
   * ----------------------------------------------------------
   */
  if (
      position === 0
  ) {
    return {
      transform:
          createTransform(
              0,
              -18,
              1,
          ),

      opacity:
          '1',

      zIndex:
          '30',

      transitionProperty:
          'transform, opacity',

      transitionDuration:
          isMobile.value
              ? `${MOBILE_MOVE_DURATION}ms`
              : '700ms',

      transitionTimingFunction:
          'cubic-bezier(.22,.61,.36,1)',

      willChange:
          'transform, opacity',
    }
  }

  /**
   * ----------------------------------------------------------
   * LEFT
   * ----------------------------------------------------------
   */
  if (
      position === -1
  ) {
    return {
      transform:
          createTransform(
              -sideDistance,
              sideY,
              sideScale,
          ),

      opacity:
          isMobile.value
              ? '0.52'
              : '0.72',

      zIndex:
          '20',

      transitionProperty:
          'transform, opacity',

      transitionDuration:
          isMobile.value
              ? `${MOBILE_MOVE_DURATION}ms`
              : '700ms',

      transitionTimingFunction:
          'cubic-bezier(.22,.61,.36,1)',

      willChange:
          'transform, opacity',
    }
  }

  /**
   * ----------------------------------------------------------
   * RIGHT
   * ----------------------------------------------------------
   */
  if (
      position === 1
  ) {
    return {
      transform:
          createTransform(
              sideDistance,
              sideY,
              sideScale,
          ),

      opacity:
          isMobile.value
              ? '0.52'
              : '0.72',

      zIndex:
          '20',

      transitionProperty:
          'transform, opacity',

      transitionDuration:
          isMobile.value
              ? `${MOBILE_MOVE_DURATION}ms`
              : '700ms',

      transitionTimingFunction:
          'cubic-bezier(.22,.61,.36,1)',

      willChange:
          'transform, opacity',
    }
  }

  /**
   * ----------------------------------------------------------
   * FALLBACK
   * ----------------------------------------------------------
   */
  return {
    transform:
        createTransform(
            0,
            80,
            0.45,
        ),

    opacity:
        '0',

    zIndex:
        '5',
  }
}

/**
 * ============================================================
 * TROPHY IMAGE STATE
 * ============================================================
 *
 * iOS:
 *
 * Ne animiramo filter,
 * scale niti drop-shadow.
 *
 * Desktop / Android:
 *
 * Zadržavamo bogatiji izgled.
 */
const getTrophyImageClass = (
    index: number,
) => {
  /**
   * iOS WebKit safe mode.
   */
  if (
      isIOS.value
  ) {
    return isActive(index)
        ? 'opacity-100'
        : 'opacity-75'
  }

  /**
   * Desktop / Android active.
   */
  if (
      isActive(index)
  ) {
    return `
      trophy-active
      brightness-110
      contrast-105
      drop-shadow-[0_24px_38px_rgba(3,20,38,0.28)]
    `
  }

  /**
   * Desktop / Android side trophy.
   */
  return `
    grayscale
    brightness-[0.75]
    contrast-95
    opacity-75
    drop-shadow-[0_16px_22px_rgba(3,20,38,0.20)]
    transition-[filter,opacity]
    duration-300
  `
}

/**
 * ============================================================
 * MOBILE ROTATION
 * ============================================================
 */
const startMobileRotation = (
    direction:
    Exclude<
        RotationDirection,
        null
    >,
) => {
  if (
      isRotating.value
  ) {
    return
  }

  clearTimers()

  isRotating.value =
      true

  rotationDirection.value =
      direction

  const oldIndex =
      activeIndex.value

  /**
   * Wrapped trophy.
   *
   * NEXT:
   * old LEFT.
   *
   * PREV:
   * old RIGHT.
   */
  wrappingIndex.value =
      direction === 'next'
          ? (
          oldIndex -
          1 +
          trophies.length
      ) % trophies.length
          : (
          oldIndex +
          1
      ) % trophies.length

  /**
   * Start hide phase
   * prije promjene active indexa.
   */
  wrapPhase.value =
      'hide'

  /**
   * Dva RAF-a omogućavaju browseru
   * da registruje početnu fazu
   * prije promjene pozicija.
   */
  requestAnimationFrame(
      () => {
        requestAnimationFrame(
            () => {
              activeIndex.value =
                  direction ===
                  'next'
                      ? (
                          oldIndex +
                          1
                      ) %
                      trophies.length
                      : (
                          oldIndex -
                          1 +
                          trophies.length
                      ) %
                      trophies.length
            },
        )
      },
  )

  /**
   * TELEPORT
   */
  addTimer(
      () => {
        wrapPhase.value =
            'teleport'
      },
      WRAP_HIDE_DURATION,
  )

  /**
   * SHOW
   */
  addTimer(
      () => {
        requestAnimationFrame(
            () => {
              requestAnimationFrame(
                  () => {
                    wrapPhase.value =
                        'show'
                  },
              )
            },
        )
      },
      WRAP_HIDE_DURATION +
      TELEPORT_SETTLE,
  )

  /**
   * FINISH
   */
  addTimer(
      () => {
        wrappingIndex.value =
            null

        rotationDirection.value =
            null

        wrapPhase.value =
            'idle'

        isRotating.value =
            false
      },
      MOBILE_ROTATION_DURATION,
  )
}

/**
 * ============================================================
 * NEXT
 * ============================================================
 */
const next = () => {
  if (
      isRotating.value
  ) {
    return
  }

  /**
   * Mobile safe animation.
   */
  if (
      isMobile.value
  ) {
    startMobileRotation(
        'next',
    )

    return
  }

  /**
   * Desktop / tablet.
   */
  activeIndex.value =
      (
          activeIndex.value +
          1
      ) %
      trophies.length
}

/**
 * ============================================================
 * PREVIOUS
 * ============================================================
 */
const prev = () => {
  if (
      isRotating.value
  ) {
    return
  }

  if (
      isMobile.value
  ) {
    startMobileRotation(
        'prev',
    )

    return
  }

  activeIndex.value =
      (
          activeIndex.value -
          1 +
          trophies.length
      ) %
      trophies.length
}

/**
 * ============================================================
 * ACTIVATE TROPHY
 * ============================================================
 */
const activate = (
    index: number,
) => {
  if (
      index < 0 ||
      index >= trophies.length ||
      index ===
      activeIndex.value ||
      isRotating.value
  ) {
    return
  }

  const relative =
      getRelativePosition(
          index,
      )

  if (
      relative === 1
  ) {
    next()
    return
  }

  if (
      relative === -1
  ) {
    prev()
    return
  }

  activeIndex.value =
      index
}

/**
 * ============================================================
 * DRAG / SWIPE
 * ============================================================
 */
const isDragging =
    ref(false)

const startX =
    ref(0)

const currentX =
    ref(0)

const dragDistance =
    ref(0)

/**
 * ------------------------------------------------------------
 * POINTER DOWN
 * ------------------------------------------------------------
 */
const onPointerDown = (
    event: PointerEvent,
) => {
  if (
      isRotating.value
  ) {
    return
  }

  const target =
      event.target as HTMLElement

  /**
   * Buttons/links must keep
   * their normal behavior.
   */
  if (
      target.closest(
          'button, a, input, textarea, select, [data-no-drag]',
      )
  ) {
    return
  }

  isDragging.value =
      true

  startX.value =
      event.clientX

  currentX.value =
      event.clientX

  dragDistance.value =
      0

  const carousel =
      event.currentTarget as HTMLElement

  carousel.setPointerCapture?.(
      event.pointerId,
  )
}

/**
 * ------------------------------------------------------------
 * POINTER MOVE
 * ------------------------------------------------------------
 */
const onPointerMove = (
    event: PointerEvent,
) => {
  if (
      !isDragging.value
  ) {
    return
  }

  currentX.value =
      event.clientX

  dragDistance.value =
      currentX.value -
      startX.value
}

/**
 * ------------------------------------------------------------
 * POINTER UP
 * ------------------------------------------------------------
 */
const onPointerUp = (
    event: PointerEvent,
) => {
  if (
      !isDragging.value
  ) {
    return
  }

  const threshold =
      isMobile.value
          ? 42
          : 55

  /**
   * Swipe right.
   */
  if (
      dragDistance.value >
      threshold
  ) {
    prev()
  }

  /**
   * Swipe left.
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

  isDragging.value =
      false

  dragDistance.value =
      0
}

/**
 * ============================================================
 * LIFECYCLE
 * ============================================================
 */
onMounted(
    () => {
      updateViewportWidth()

      detectIOS()

      window.addEventListener(
          'resize',
          updateViewportWidth,
          {
            passive: true,
          },
      )
    },
)

onBeforeUnmount(
    () => {
      clearTimers()

      window.removeEventListener(
          'resize',
          updateViewportWidth,
      )
    },
)
</script>

<style scoped>
/**
 * ============================================================
 * TROPHY WRAPPER
 * ============================================================
 *
 * Jedini element koji kontroliše
 * poziciju i scale.
 */
.trophy-item {
  transform-origin:
      center center;

  -webkit-backface-visibility:
      hidden;

  backface-visibility:
      hidden;

  will-change:
      transform,
      opacity;
}

/**
 * Image ne dobija transform.
 *
 * Ovo je posebno važno za Safari/iOS.
 */
.trophy-image {
  -webkit-backface-visibility:
      hidden;

  backface-visibility:
      hidden;
}

/**
 * ============================================================
 * ACTIVE TROPHY EFFECT
 *
 * Desktop / Android.
 *
 * Na iOS-u ova klasa se ne dodjeljuje.
 * ============================================================
 */
@keyframes trophyReveal {
  0% {
    filter:
        brightness(0.96)
        contrast(1)
        drop-shadow(
            0 0 0
            rgba(
                216,
                173,
                89,
                0
            )
        );
  }

  100% {
    filter:
        brightness(1.1)
        contrast(1.05)
        drop-shadow(
            0 0 14px
            rgba(
                216,
                173,
                89,
                0.20
            )
        );
  }
}

.trophy-active {
  animation:
      trophyReveal
      0.35s
      ease-out
      forwards;
}

/**
 * ============================================================
 * INFO TRANSITION
 * ============================================================
 */
.trophy-info-enter-active,
.trophy-info-leave-active {
  transition:
      opacity 0.28s ease,
      transform 0.28s ease;
}

.trophy-info-enter-from {
  opacity: 0;

  transform:
      translateY(10px);
}

.trophy-info-leave-to {
  opacity: 0;

  transform:
      translateY(-6px);
}

/**
 * ============================================================
 * IOS / MOBILE
 * ============================================================
 */
@media (
max-width: 639px
) {
  .trophy-item {
    -webkit-backface-visibility:
        hidden;

    backface-visibility:
        hidden;
  }

  .trophy-image {
    -webkit-backface-visibility:
        hidden;

    backface-visibility:
        hidden;
  }
}

/**
 * ============================================================
 * REDUCED MOTION
 * ============================================================
 */
@media (
prefers-reduced-motion:
    reduce
) {
  .trophy-active {
    animation: none;
  }

  .trophy-info-enter-active,
  .trophy-info-leave-active {
    transition: none;
  }
}
</style>