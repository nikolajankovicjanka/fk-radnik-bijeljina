<script setup lang="ts">
  import {
    ArrowLeft,
    CheckCircle2,
    LoaderCircle,
    Minus,
    Plus,
    ShieldCheck,
    ShoppingBag,
    Trash2,
    TriangleAlert,
    Truck,
  } from 'lucide-vue-next'
  import { computed, onBeforeUnmount, ref, watch } from 'vue'

  import { useCartStore } from '@/stores/cart'
  import { ShopApiError, shopService } from '@/services/shopService'

  import type { ShopPricingData, ShopPricingItem } from '@/types/shop'

  const cartStore = useCartStore()

  const pricing = ref<ShopPricingData | null>(null)
  const pricingLoading = ref(false)
  const pricingError = ref('')
  const pricingVerified = ref(false)

  let pricingTimer: ReturnType<typeof setTimeout> | null = null
  let pricingRequestId = 0

  const backendSubtotal = computed(() => {
    return pricing.value?.subtotal ?? cartStore.subtotal
  })

  const canCheckout = computed(() => {
    return (
      !cartStore.isEmpty &&
      !pricingLoading.value &&
      pricingVerified.value &&
      !pricingError.value &&
      pricing.value !== null
    )
  })

  function formatPrice(value: number) {
    return `${Number(value).toFixed(2).replace('.', ',')} KM`
  }

  /*
|--------------------------------------------------------------------------
| Pricing payload
|--------------------------------------------------------------------------
|
| Isti variant može postojati više puta u korpi:
|
| - običan dres S
| - personalizovan dres S
|
| Backend ih svakako sabira, ali ih sabiramo i ovdje da request bude čist.
|
*/

  function getPricingItems(): ShopPricingItem[] {
    const quantities = new Map<number, number>()

    for (const item of cartStore.items) {
      quantities.set(item.variantId, (quantities.get(item.variantId) ?? 0) + item.quantity)
    }

    return Array.from(quantities.entries()).map(([variantId, quantity]) => ({
      variant_id: variantId,
      quantity,
    }))
  }

  /*
|--------------------------------------------------------------------------
| Backend pricing validation
|--------------------------------------------------------------------------
*/

  async function validateCart() {
    if (cartStore.isEmpty) {
      pricing.value = null
      pricingError.value = ''
      pricingVerified.value = false
      pricingLoading.value = false

      return
    }

    const currentRequestId = ++pricingRequestId

    pricingLoading.value = true
    pricingError.value = ''
    pricingVerified.value = false

    try {
      const response = await shopService.calculatePricing({
        items: getPricingItems(),

        /*
         * Popusti se biraju na checkoutu.
         */
        season_ticket_number: null,
        voucher_code: null,

        /*
         * Korpa još nema odabran način dostave.
         * Pickup = 0 shipping i koristimo ga samo da
         * provjerimo artikle, cijenu i stanje.
         */
        delivery_method: 'pickup',
      })

      /*
       * Ako je korisnik u međuvremenu promijenio
       * količinu, ignoriši odgovor starog requesta.
       */
      if (currentRequestId !== pricingRequestId) {
        return
      }

      pricing.value = response.data
      pricingVerified.value = true
    } catch (error) {
      if (currentRequestId !== pricingRequestId) {
        return
      }

      pricing.value = null
      pricingVerified.value = false

      if (error instanceof ShopApiError) {
        pricingError.value = error.message
      } else {
        pricingError.value = 'Nije moguće provjeriti korpu. Pokušajte ponovo.'
      }
    } finally {
      if (currentRequestId === pricingRequestId) {
        pricingLoading.value = false
      }
    }
  }

  /*
|--------------------------------------------------------------------------
| Debounce
|--------------------------------------------------------------------------
*/

  function scheduleCartValidation() {
    pricingVerified.value = false

    if (pricingTimer) {
      clearTimeout(pricingTimer)
    }

    pricingTimer = setTimeout(() => {
      void validateCart()
    }, 350)
  }

  watch(
    () =>
      cartStore.items.map(item => ({
        key: item.key,
        variantId: item.variantId,
        quantity: item.quantity,
      })),
    () => {
      scheduleCartValidation()
    },
    {
      deep: true,
      immediate: true,
    }
  )

  onBeforeUnmount(() => {
    if (pricingTimer) {
      clearTimeout(pricingTimer)
    }

    /*
     * Invalidiramo eventualni request koji još traje.
     */
    pricingRequestId++
  })
</script>

<template>
  <div class="cart-page min-h-screen bg-[#F4F7F9]">
    <!-- HEADER -->
    <section class="border-b border-[#DCEAF4] bg-[#031426]">
      <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:py-14">
        <div
          class="mb-3 flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.14em] text-[#edc06a]"
        >
          <ShoppingBag :size="15" />
          FK Radnik Shop
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h1
              class="cart-heading text-[42px] font-black uppercase leading-none tracking-[-0.025em] text-white sm:text-[56px]"
            >
              Moja korpa
            </h1>

            <p v-if="!cartStore.isEmpty" class="mt-3 text-sm text-white/55">
              {{ cartStore.totalItems }}
              {{ cartStore.totalItems === 1 ? 'artikal u korpi' : 'artikala u korpi' }}
            </p>
          </div>

          <RouterLink
            to="/shop"
            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.1em] text-white/70 transition hover:text-[#edc06a]"
          >
            <ArrowLeft :size="14" />
            Nastavi kupovinu
          </RouterLink>
        </div>
      </div>
    </section>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
      <!-- EMPTY CART -->
      <div
        v-if="cartStore.isEmpty"
        class="mx-auto max-w-2xl rounded-lg border border-[#DCEAF4] bg-white px-6 py-16 text-center shadow-sm"
      >
        <div
          class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#DCEAF4] text-[#006faf]"
        >
          <ShoppingBag :size="27" />
        </div>

        <h2 class="cart-heading mt-6 text-3xl font-black uppercase text-[#031426]">
          Korpa je prazna
        </h2>

        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-[#62666c]">
          Trenutno nemaš proizvoda u korpi. Pogledaj zvanične proizvode FK Radnik Bijeljina.
        </p>

        <RouterLink
          to="/shop"
          class="mt-7 inline-flex h-12 items-center justify-center bg-[#006faf] px-7 text-[11px] font-black uppercase tracking-[0.1em] text-white transition hover:bg-[#005d94]"
        >
          Pogledaj proizvode
        </RouterLink>
      </div>

      <!-- CART -->
      <div v-else class="grid items-start gap-8 lg:grid-cols-[1fr_360px] xl:grid-cols-[1fr_390px]">
        <!-- PRODUCTS -->
        <section>
          <div class="mb-4 flex items-center justify-between">
            <h2 class="cart-heading text-2xl font-black uppercase text-[#031426]">Proizvodi</h2>

            <button
              type="button"
              class="text-[9px] font-black uppercase tracking-[0.08em] text-[#8e9197] transition hover:text-red-600"
              @click="cartStore.clearCart()"
            >
              Isprazni korpu
            </button>
          </div>

          <!-- PRICING STATUS MOBILE/DESKTOP -->
          <div
            v-if="pricingLoading"
            class="mb-4 flex items-center gap-3 rounded-lg border border-[#DCEAF4] bg-white px-4 py-3 text-xs text-[#62666c]"
          >
            <LoaderCircle :size="17" class="animate-spin text-[#006faf]" />

            Provjeravamo cijenu i stanje proizvoda...
          </div>

          <div
            v-else-if="pricingError"
            class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3"
          >
            <TriangleAlert :size="18" class="mt-0.5 flex-shrink-0 text-red-600" />

            <div>
              <p class="text-[10px] font-black uppercase tracking-[0.06em] text-red-700">
                Provjera korpe nije prošla
              </p>

              <p class="mt-1 text-xs leading-5 text-red-700/80">
                {{ pricingError }}
              </p>

              <button
                type="button"
                class="mt-2 text-[9px] font-black uppercase tracking-[0.08em] text-red-700 underline underline-offset-2"
                @click="validateCart"
              >
                Pokušaj ponovo
              </button>
            </div>
          </div>

          <div
            v-else-if="pricingVerified"
            class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3"
          >
            <CheckCircle2 :size="18" class="flex-shrink-0 text-emerald-600" />

            <p class="text-[10px] font-black uppercase tracking-[0.06em] text-emerald-700">
              Cijena i stanje proizvoda su provjereni
            </p>
          </div>

          <!-- ITEMS -->
          <div class="grid gap-4">
            <article
              v-for="item in cartStore.items"
              :key="item.key"
              class="overflow-hidden rounded-lg border border-[#DCEAF4] bg-white shadow-sm"
            >
              <div
                class="grid grid-cols-[105px_1fr] gap-4 p-4 sm:grid-cols-[140px_1fr] sm:gap-5 sm:p-5"
              >
                <!-- IMAGE -->
                <RouterLink
                  :to="{
                    name: 'ShopProduct',
                    params: {
                      slug: item.slug,
                    },
                  }"
                  class="relative aspect-[4/5] overflow-hidden rounded bg-[#F4F7F9]"
                >
                  <img
                    v-if="item.image"
                    :src="item.image"
                    :alt="item.name"
                    class="h-full w-full object-contain p-2"
                  />

                  <div v-else class="flex h-full items-center justify-center">
                    <img
                      src="/logo/FK_Radnik_logo.png"
                      alt=""
                      class="w-16 opacity-[0.12] grayscale"
                    />
                  </div>
                </RouterLink>

                <!-- INFO -->
                <div class="flex min-w-0 flex-col">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                      <p class="text-[9px] font-bold uppercase tracking-[0.1em] text-[#006faf]">
                        Veličina:
                        {{ item.variantSize }}
                      </p>

                      <RouterLink
                        :to="{
                          name: 'ShopProduct',
                          params: {
                            slug: item.slug,
                          },
                        }"
                      >
                        <h3
                          class="cart-heading mt-1 line-clamp-2 text-xl font-black uppercase leading-[1.05] text-[#031426] transition hover:text-[#006faf] sm:text-2xl"
                        >
                          {{ item.name }}
                        </h3>
                      </RouterLink>

                      <p class="mt-2 text-[9px] uppercase tracking-[0.06em] text-[#8e9197]">
                        {{ item.variantSku }}
                      </p>
                    </div>

                    <button
                      type="button"
                      :aria-label="`Ukloni ${item.name}`"
                      class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-[#8e9197] transition hover:bg-red-50 hover:text-red-600"
                      @click="cartStore.removeItem(item.key)"
                    >
                      <Trash2 :size="16" />
                    </button>
                  </div>

                  <!-- CUSTOMIZATION -->
                  <div
                    v-if="item.customization"
                    class="mt-3 inline-flex w-fit flex-wrap items-center gap-x-4 gap-y-1 rounded bg-[#edc06a]/10 px-3 py-2 text-[10px] text-[#70551c]"
                  >
                    <span v-if="item.customization.name">
                      Ime:
                      <strong>
                        {{ item.customization.name }}
                      </strong>
                    </span>

                    <span v-if="item.customization.number">
                      Broj:
                      <strong>
                        {{ item.customization.number }}
                      </strong>
                    </span>
                  </div>

                  <!-- BOTTOM -->
                  <div
                    class="mt-auto flex flex-col gap-4 pt-5 sm:flex-row sm:items-end sm:justify-between"
                  >
                    <!-- QUANTITY -->
                    <div>
                      <p
                        class="mb-2 text-[9px] font-black uppercase tracking-[0.08em] text-[#8e9197]"
                      >
                        Količina
                      </p>

                      <div
                        class="flex h-9 w-[112px] overflow-hidden rounded border border-[#DCEAF4]"
                      >
                        <button
                          type="button"
                          aria-label="Smanji količinu"
                          class="flex w-9 items-center justify-center text-[#072846] transition hover:bg-[#F4F7F9]"
                          @click="cartStore.decreaseQuantity(item.key)"
                        >
                          <Minus :size="13" />
                        </button>

                        <div
                          class="flex flex-1 items-center justify-center border-x border-[#DCEAF4] text-[11px] font-black text-[#031426]"
                        >
                          {{ item.quantity }}
                        </div>

                        <button
                          type="button"
                          :disabled="item.quantity >= item.availableQuantity"
                          aria-label="Povećaj količinu"
                          class="flex w-9 items-center justify-center text-[#072846] transition hover:bg-[#F4F7F9] disabled:cursor-not-allowed disabled:opacity-30"
                          @click="cartStore.increaseQuantity(item.key)"
                        >
                          <Plus :size="13" />
                        </button>
                      </div>
                    </div>

                    <!-- PRICE -->
                    <div class="sm:text-right">
                      <div
                        v-if="item.originalPrice"
                        class="mb-1 text-[10px] text-[#8e9197] line-through"
                      >
                        {{ formatPrice(item.originalPrice * item.quantity) }}
                      </div>

                      <div class="text-xl font-black text-[#006faf]">
                        {{ formatPrice(item.unitPrice * item.quantity) }}
                      </div>

                      <div v-if="item.quantity > 1" class="mt-1 text-[9px] text-[#8e9197]">
                        {{ formatPrice(item.unitPrice) }}
                        / kom.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </article>
          </div>
        </section>

        <!-- SUMMARY -->
        <aside class="lg:sticky lg:top-[110px]">
          <div class="overflow-hidden rounded-lg bg-[#031426] shadow-sm">
            <div class="border-b border-white/10 px-6 py-5">
              <p class="text-[9px] font-black uppercase tracking-[0.14em] text-[#edc06a]">
                FK Radnik Shop
              </p>

              <h2 class="cart-heading mt-1 text-2xl font-black uppercase text-white">
                Pregled korpe
              </h2>
            </div>

            <div class="px-6 py-6">
              <div class="flex items-center justify-between text-sm">
                <span class="text-white/55"> Artikli ({{ cartStore.totalItems }}) </span>

                <span class="font-bold text-white">
                  {{ formatPrice(backendSubtotal) }}
                </span>
              </div>

              <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-white/55"> Dostava </span>

                <span class="text-xs font-semibold text-white/70"> Bira se na checkoutu </span>
              </div>

              <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-white/55"> Popusti </span>

                <span class="text-xs font-semibold text-white/70">
                  Primjenjuju se na checkoutu
                </span>
              </div>

              <div class="my-6 border-t border-white/10" />

              <div class="flex items-end justify-between gap-4">
                <div>
                  <p class="text-[9px] font-black uppercase tracking-[0.1em] text-white/50">
                    Međuzbir
                  </p>

                  <p class="cart-heading mt-1 text-lg font-black uppercase text-white">Proizvodi</p>
                </div>

                <div class="text-[26px] font-black leading-none text-[#edc06a]">
                  {{ formatPrice(backendSubtotal) }}
                </div>
              </div>

              <!-- VALIDATION STATUS -->
              <div
                v-if="pricingLoading"
                class="mt-6 flex items-center gap-2 rounded bg-white/5 px-3 py-2.5"
              >
                <LoaderCircle :size="14" class="animate-spin text-[#edc06a]" />

                <span class="text-[9px] font-semibold text-white/55"> Provjera korpe... </span>
              </div>

              <div
                v-else-if="pricingError"
                class="mt-6 flex items-start gap-2 rounded bg-red-500/10 px-3 py-2.5"
              >
                <TriangleAlert :size="14" class="mt-0.5 flex-shrink-0 text-red-400" />

                <span class="text-[9px] leading-4 text-red-200">
                  {{ pricingError }}
                </span>
              </div>

              <div
                v-else-if="pricingVerified"
                class="mt-6 flex items-center gap-2 rounded bg-emerald-500/10 px-3 py-2.5"
              >
                <CheckCircle2 :size="14" class="text-emerald-400" />

                <span class="text-[9px] font-semibold text-emerald-200">
                  Cijena i stanje potvrđeni
                </span>
              </div>

              <!-- CHECKOUT -->
              <RouterLink
                v-if="canCheckout"
                to="/shop/checkout"
                class="mt-5 flex h-[52px] w-full items-center justify-center gap-2 bg-[#006faf] px-5 text-[11px] font-black uppercase tracking-[0.1em] text-white transition hover:bg-[#0082c9]"
              >
                Nastavi na naplatu
              </RouterLink>

              <button
                v-else
                type="button"
                disabled
                class="mt-5 flex h-[52px] w-full cursor-not-allowed items-center justify-center gap-2 bg-[#006faf] px-5 text-[11px] font-black uppercase tracking-[0.1em] text-white opacity-40"
              >
                Nastavi na naplatu
              </button>

              <p class="mt-3 text-center text-[9px] leading-4 text-white/40">
                Dostavu i eventualni popust biraš u sljedećem koraku.
              </p>
            </div>
          </div>

          <!-- INFO -->
          <div class="mt-4 grid gap-3 rounded-lg border border-[#DCEAF4] bg-white p-5">
            <div class="flex items-start gap-3">
              <ShieldCheck :size="18" class="mt-0.5 flex-shrink-0 text-[#006faf]" />

              <div>
                <p class="text-[10px] font-black uppercase tracking-[0.06em] text-[#072846]">
                  Backend provjera
                </p>

                <p class="mt-1 text-[10px] leading-4 text-[#8e9197]">
                  Cijena i raspoloživo stanje provjeravaju se direktno na serveru.
                </p>
              </div>
            </div>

            <div class="border-t border-[#DCEAF4]" />

            <div class="flex items-start gap-3">
              <Truck :size="18" class="mt-0.5 flex-shrink-0 text-[#006faf]" />

              <div>
                <p class="text-[10px] font-black uppercase tracking-[0.06em] text-[#072846]">
                  Dostava ili preuzimanje
                </p>

                <p class="mt-1 text-[10px] leading-4 text-[#8e9197]">
                  Način preuzimanja bira se prilikom završetka narudžbe.
                </p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<style scoped>
  .cart-page {
    font-family:
      'Hanken Grotesk',
      system-ui,
      -apple-system,
      BlinkMacSystemFont,
      'Segoe UI',
      sans-serif;
  }

  .cart-heading {
    font-family: 'Archivo Narrow', 'Arial Narrow', Arial, sans-serif;
  }
</style>
