<script setup lang="ts">
  import { ArrowRight, Check, Mail, PackageCheck, ShoppingBag } from 'lucide-vue-next'

  import { computed } from 'vue'
  import { useRoute } from 'vue-router'

  const route = useRoute()

  const orderNumber = computed(() => String(route.query.order ?? ''))

  const total = computed(() => {
    const value = Number(route.query.total ?? 0)

    return `${value.toFixed(2).replace('.', ',')} KM`
  })

  const isPickup = computed(() => route.query.delivery === 'pickup')
</script>

<template>
  <div class="min-h-[75vh] bg-[#031426] px-4 py-16 sm:px-6 lg:py-24">
    <div class="mx-auto max-w-2xl text-center">
      <div
        class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#edc06a] text-[#031426]"
      >
        <Check :size="34" :stroke-width="3" />
      </div>

      <p class="mt-8 text-[9px] font-black uppercase tracking-[0.16em] text-[#edc06a]">
        FK Radnik Shop
      </p>

      <h1
        class="mt-2 font-['Archivo_Narrow'] text-[42px] font-black uppercase leading-none text-white sm:text-[58px]"
      >
        Narudžba je zaprimljena
      </h1>

      <p class="mx-auto mt-5 max-w-lg text-sm leading-6 text-white/50">
        Hvala na narudžbi. Podaci o narudžbi poslani su na vašu email adresu.
      </p>

      <div class="mt-8 overflow-hidden rounded-xl border border-white/10 bg-[#072846] text-left">
        <div class="border-b border-white/10 px-5 py-5 sm:px-6">
          <p class="text-[9px] font-black uppercase tracking-[0.1em] text-white/40">
            Broj narudžbe
          </p>

          <p class="mt-1 text-xl font-black text-[#edc06a]">
            {{ orderNumber }}
          </p>
        </div>

        <div class="grid gap-5 px-5 py-5 sm:grid-cols-2 sm:px-6">
          <div class="flex items-start gap-3">
            <PackageCheck :size="18" class="mt-0.5 text-[#006faf]" />

            <div>
              <p class="text-[9px] font-black uppercase tracking-[0.08em] text-white/40">Ukupno</p>

              <p class="mt-1 font-bold text-white">
                {{ total }}
              </p>
            </div>
          </div>

          <div class="flex items-start gap-3">
            <ShoppingBag :size="18" class="mt-0.5 text-[#006faf]" />

            <div>
              <p class="text-[9px] font-black uppercase tracking-[0.08em] text-white/40">
                Preuzimanje
              </p>

              <p class="mt-1 text-xs font-bold text-white">
                {{ isPickup ? 'Lično preuzimanje' : 'Kurirska dostava' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div
        class="mt-5 flex items-start gap-3 rounded-lg border border-white/10 bg-white/[0.04] px-4 py-4 text-left"
      >
        <Mail :size="18" class="mt-0.5 flex-shrink-0 text-[#edc06a]" />

        <p class="text-[10px] leading-5 text-white/45">
          Sačuvajte broj narudžbe. Potvrdu narudžbe dobićete i putem emaila.
        </p>
      </div>

      <RouterLink
        to="/shop"
        class="mt-8 inline-flex h-12 items-center justify-center gap-2 bg-[#edc06a] px-7 text-[10px] font-black uppercase tracking-[0.1em] text-[#031426] transition hover:bg-[#f5cd7c]"
      >
        Nazad u webshop
        <ArrowRight :size="14" />
      </RouterLink>
    </div>
  </div>
</template>
