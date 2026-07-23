<template>
  <Teleport to="body">
    <Transition name="promo-modal">
      <div
          v-if="isVisible"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-3 backdrop-blur-sm sm:p-6"
          role="dialog"
          aria-modal="true"
          aria-label="Sezonske karte FK Radnik Bijeljina"
          @click.self="closeModal"
      >
        <div
            class="relative w-full max-w-6xl overflow-hidden rounded-2xl bg-[#071f36] shadow-2xl"
        >
          <!-- Close button -->
          <button
              type="button"
              class="absolute right-3 top-3 z-40 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-2xl font-normal text-white backdrop-blur transition hover:bg-black/80"
              aria-label="Zatvori reklamu"
              @click="closeModal"
          >
            ×
          </button>

          <!-- Loading dok se slika ne učita -->
          <div
              v-if="!isImageLoaded"
              class="flex min-h-[280px] w-full flex-col items-center justify-center gap-4 bg-[#071f36] sm:min-h-[420px]"
          >
            <div
                class="h-10 w-10 animate-spin rounded-full border-4 border-white/20 border-t-[#2aa2ff]"
            ></div>

            <p class="text-sm font-medium text-white/80">
              Učitavanje ponude...
            </p>
          </div>

          <!-- Banner -->
          <div
              v-show="isImageLoaded"
              class="relative w-full overflow-hidden bg-[#071f36]"
          >
            <img
                ref="bannerImage"
                :src="imageSrc"
                :alt="title"
                class="block h-auto max-h-[85vh] w-full object-contain"
                @load="handleImageLoad"
                @error="handleImageError"
            />

            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-r from-[#071f36]/80 via-[#071f36]/30 to-transparent"
            ></div>

            <!-- Content -->
            <div class="absolute inset-0">
              <div
                  class="relative mx-auto flex h-full w-full max-w-7xl items-end px-4 pb-4 sm:items-center sm:px-6 sm:pb-0 lg:px-8"
              >
                <!-- Badge: skriven na mobilnim uređajima -->
                <div
                    v-if="badgeText"
                    class="absolute left-6 top-6 hidden items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold tracking-widest text-white backdrop-blur sm:inline-flex lg:left-8"
                >
                  {{ badgeText }}
                </div>

                <div class="max-w-xl">
                  <!-- Tekst: skriven na mobilnim uređajima -->
                  <div class="hidden sm:block">
                    <h3
                        class="text-3xl font-extrabold uppercase tracking-wide text-white lg:text-4xl"
                    >
                      {{ title }}
                    </h3>

                    <p
                        v-if="subtitle"
                        class="mt-2 text-sm text-white/90 sm:text-base"
                    >
                      {{ subtitle }}
                    </p>
                  </div>

                  <!-- Buttons -->
                  <div class="flex flex-wrap gap-2 sm:mt-5 sm:gap-3">
                    <a
                        :href="ticketMailto"
                        class="inline-flex items-center justify-center rounded-lg bg-[#2aa2ff] px-3 py-2 text-xs font-bold text-white transition hover:brightness-110 sm:rounded-xl sm:px-5 sm:py-3 sm:text-sm"
                        aria-label="Kupi sezonsku kartu putem emaila"
                        @click="closeModal"
                    >
                      {{ primaryText }}

                      <span class="ml-1">→</span>
                    </a>

                    <a
                        v-if="phoneHref"
                        :href="phoneHref"
                        class="inline-flex items-center justify-center rounded-lg bg-white/15 px-3 py-2 text-xs font-bold text-white backdrop-blur transition hover:bg-white/25 sm:rounded-xl sm:px-5 sm:py-3 sm:text-sm"
                        aria-label="Pozovi FK Radnik Bijeljina"
                        @click="closeModal"
                    >
                      {{ secondaryText }}
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Glow -->
            <div
                class="pointer-events-none absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-[#2aa2ff]/20 blur-3xl"
            ></div>

            <!-- Odbrojavanje počinje tek kada se slika učita -->
            <div
                v-if="countdownStarted"
                class="absolute inset-x-0 bottom-0 z-30 h-1 bg-white/20"
            >
              <div
                  class="promo-progress h-full bg-[#2aa2ff]"
                  :style="{
                  animationDuration: `${duration}ms`,
                }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import {
  computed,
  nextTick,
  onBeforeUnmount,
  onMounted,
  ref,
} from 'vue'

const props = withDefaults(
    defineProps<{
      imageSrc: string
      title: string
      subtitle?: string
      badgeText?: string

      duration?: number
      storageKey?: string

      ticketEmail?: string
      primaryText?: string

      phoneNumber?: string
      secondaryText?: string
    }>(),
    {
      subtitle: '',
      badgeText: '',

      // 10 sekundi nakon učitavanja slike
      duration: 10000,

      storageKey: 'fk-radnik-season-ticket-popup-2026-27',

      ticketEmail: 'office@fkradnikbijeljina.com',
      primaryText: 'Kupi sezonsku',

      phoneNumber: '',
      secondaryText: 'Kontakt',
    }
)

const isVisible = ref(false)
const isImageLoaded = ref(false)
const countdownStarted = ref(false)

const bannerImage = ref<HTMLImageElement | null>(null)

let closeTimer: ReturnType<typeof setTimeout> | null = null
let previousBodyOverflow = ''

const ticketMailto = computed(() => {
  const subject = encodeURIComponent(
      'Kupovina sezonske karte FK Radnik Bijeljina'
  )

  const body = encodeURIComponent(`Poštovani,

Želim da kupim sezonsku kartu FK Radnik Bijeljina.

Ime i prezime:
Adresa stanovanja:
Adresa dostave:
`)

  return `mailto:${props.ticketEmail}?subject=${subject}&body=${body}`
})

const phoneHref = computed(() => {
  if (!props.phoneNumber) {
    return ''
  }

  const normalizedPhone = props.phoneNumber.replace(/[^\d+]/g, '')

  return `tel:${normalizedPhone}`
})

const startCountdown = (): void => {
  if (
      !isVisible.value ||
      !isImageLoaded.value ||
      countdownStarted.value
  ) {
    return
  }

  countdownStarted.value = true

  closeTimer = setTimeout(() => {
    closeModal()
  }, props.duration)
}

const handleImageLoad = (): void => {
  isImageLoaded.value = true

  /*
   * nextTick osigurava da progress bar prvo bude prikazan,
   * pa tek onda počinje njegovo CSS odbrojavanje.
   */
  nextTick(() => {
    startCountdown()
  })
}

const handleImageError = (): void => {
  console.error(`Banner slika nije učitana: ${props.imageSrc}`)

  closeModal()
}

const closeModal = (): void => {
  isVisible.value = false

  if (closeTimer) {
    clearTimeout(closeTimer)
    closeTimer = null
  }

  document.body.style.overflow = previousBodyOverflow

  window.removeEventListener('keydown', handleKeydown)
}

const handleKeydown = (event: KeyboardEvent): void => {
  if (event.key === 'Escape') {
    closeModal()
  }
}

onMounted(() => {
  const wasAlreadyShown = sessionStorage.getItem(props.storageKey)

  if (wasAlreadyShown) {
    return
  }

  /*
   * Bilježimo prikaz odmah, kako se modal ne bi ponovo otvorio
   * kada se korisnik kroz Vue Router vrati na homepage.
   */
  sessionStorage.setItem(props.storageKey, 'true')

  isVisible.value = true

  previousBodyOverflow = document.body.style.overflow
  document.body.style.overflow = 'hidden'

  window.addEventListener('keydown', handleKeydown)

  /*
   * Kada je slika već u browser cache-u,
   * moguće je da je učitana prije registracije @load događaja.
   */
  nextTick(() => {
    if (
        bannerImage.value?.complete &&
        bannerImage.value.naturalWidth > 0
    ) {
      handleImageLoad()
    }
  })
})

onBeforeUnmount(() => {
  if (closeTimer) {
    clearTimeout(closeTimer)
  }

  document.body.style.overflow = previousBodyOverflow

  window.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.promo-modal-enter-active,
.promo-modal-leave-active {
  transition: opacity 250ms ease;
}

.promo-modal-enter-from,
.promo-modal-leave-to {
  opacity: 0;
}

.promo-modal-enter-active > div,
.promo-modal-leave-active > div {
  transition: transform 250ms ease;
}

.promo-modal-enter-from > div,
.promo-modal-leave-to > div {
  transform: scale(0.96);
}

.promo-progress {
  width: 100%;
  transform-origin: left center;
  animation-name: promo-countdown;
  animation-timing-function: linear;
  animation-fill-mode: forwards;
}

@keyframes promo-countdown {
  from {
    transform: scaleX(1);
  }

  to {
    transform: scaleX(0);
  }
}

@media (prefers-reduced-motion: reduce) {
  .promo-modal-enter-active,
  .promo-modal-leave-active,
  .promo-modal-enter-active > div,
  .promo-modal-leave-active > div {
    transition: none;
  }
}
</style>