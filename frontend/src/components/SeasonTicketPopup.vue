<template>
  <Teleport to="body">
    <Transition name="promo-modal">
      <div
          v-if="isVisible"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 p-3 backdrop-blur-sm sm:p-6"
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
              class="absolute right-3 top-3 z-30 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-xl font-bold text-white backdrop-blur transition hover:bg-black/70"
              aria-label="Zatvori reklamu"
              @click="closeModal"
          >
            ×
          </button>

          <!-- Banner -->
          <img
              :src="imageSrc"
              :alt="imageAlt"
              class="block h-auto max-h-[85vh] w-full object-contain"
          />

          <!-- Mobile bottom gradient -->
          <div
              class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-[#071f36]/95 to-transparent sm:hidden"
          ></div>

          <!-- Mobile buttons -->
          <div
              class="absolute inset-x-0 bottom-4 z-20 flex justify-center gap-2 px-4 sm:hidden"
          >
            <a
                :href="ticketMailto"
                class="inline-flex items-center justify-center rounded-lg bg-[#2aa2ff] px-4 py-2.5 text-xs font-bold text-white shadow-lg transition hover:brightness-110"
                @click="closeModal"
            >
              Kupi sezonsku →
            </a>

            <a
                v-if="phoneHref"
                :href="phoneHref"
                class="inline-flex items-center justify-center rounded-lg bg-white/20 px-4 py-2.5 text-xs font-bold text-white backdrop-blur transition hover:bg-white/30"
                @click="closeModal"
            >
              Kontakt
            </a>
          </div>

          <!-- Automatic close progress -->
          <div class="absolute inset-x-0 bottom-0 z-30 h-1 bg-white/20">
            <div
                class="promo-progress h-full bg-[#2aa2ff]"
                :style="{
                animationDuration: `${duration}ms`,
              }"
            ></div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const props = withDefaults(
    defineProps<{
      imageSrc: string
      imageAlt?: string

      duration?: number
      storageKey?: string

      ticketEmail?: string
      phoneNumber?: string
    }>(),
    {
      imageAlt: 'Sezonske karte FK Radnik Bijeljina',
      duration: 4000,
      storageKey: 'fk-radnik-season-ticket-popup-2026-27',
      ticketEmail: 'office@fkradnikbijeljina.com',
      phoneNumber: '',
    }
)

const isVisible = ref(false)

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
   * Odmah zapisujemo da je modal prikazan.
   * Zbog toga se neće ponovo pojaviti kada se korisnik
   * vrati na homepage kroz Vue Router.
   */
  sessionStorage.setItem(props.storageKey, 'true')

  isVisible.value = true

  previousBodyOverflow = document.body.style.overflow
  document.body.style.overflow = 'hidden'

  window.addEventListener('keydown', handleKeydown)

  closeTimer = setTimeout(() => {
    closeModal()
  }, props.duration)
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
  transition:
      opacity 250ms ease,
      transform 250ms ease;
}

.promo-modal-enter-from,
.promo-modal-leave-to {
  opacity: 0;
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
  .promo-modal-leave-active {
    transition: none;
  }

  .promo-progress {
    animation: none;
  }
}
</style>