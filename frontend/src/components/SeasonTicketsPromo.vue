<template>
  <section
      class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden bg-[#071f36]"
  >
    <div class="relative w-full overflow-hidden bg-[#071f36]">
      <!-- Banner image -->
      <img
          :src="imageSrc"
          :alt="title"
          class="block h-auto w-full"
          @error="handleImageError"
      />

      <!-- Overlay -->
      <div
          class="pointer-events-none absolute inset-0 bg-gradient-to-r from-[#071f36]/80 via-[#071f36]/30 to-transparent"
      ></div>

      <!-- Content -->
      <div class="absolute inset-0">
        <div
            class="relative mx-auto flex h-full w-full max-w-7xl items-end px-4 pb-3 sm:items-center sm:px-6 sm:pb-0 lg:px-8"
        >
          <!-- Badge: skriven na mobilnim uređajima -->
          <div
              v-if="badgeText"
              class="absolute left-6 top-6 hidden items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold tracking-widest text-white backdrop-blur sm:inline-flex lg:left-8"
          >
            {{ badgeText }}
          </div>

          <div class="max-w-xl">
            <!-- Naslov i opis: skriveni na mobilnim uređajima -->
            <div class="hidden sm:block">
              <h3
                  class="text-2xl font-extrabold uppercase tracking-wide text-white md:text-3xl lg:text-4xl"
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
              >
                {{ primaryText }}
                <span class="ml-1">→</span>
              </a>

              <a
                  v-if="phoneHref"
                  :href="phoneHref"
                  class="inline-flex items-center justify-center rounded-lg bg-white/15 px-3 py-2 text-xs font-bold text-white backdrop-blur transition hover:bg-white/25 sm:rounded-xl sm:px-5 sm:py-3 sm:text-sm"
                  aria-label="Pozovi FK Radnik Bijeljina"
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
    </div>
  </section>
</template>c

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
    defineProps<{
      imageSrc: string
      title: string
      subtitle?: string
      badgeText?: string

      ticketEmail?: string
      primaryText?: string

      phoneNumber?: string
      secondaryText?: string
    }>(),
    {
      subtitle: '',
      badgeText: '',
      ticketEmail: 'office@fkradnikbijeljina.com',
      primaryText: 'Kupi sezonsku',
      phoneNumber: '',
      secondaryText: 'Kontakt',
    }
)

const ticketMailto = computed<string>(() => {
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

const phoneHref = computed<string>(() => {
  if (!props.phoneNumber) {
    return ''
  }

  const normalizedPhone = props.phoneNumber.replace(/[^\d+]/g, '')

  return `tel:${normalizedPhone}`
})

const handleImageError = (): void => {
  console.error(
      `SeasonTicketsPromo: slika nije učitana: ${props.imageSrc}`
  )
}
</script>