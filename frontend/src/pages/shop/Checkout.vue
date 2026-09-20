<script setup lang="ts">
  import {
    ArrowLeft,
    BadgePercent,
    Check,
    CheckCircle2,
    CreditCard,
    LoaderCircle,
    MapPin,
    PackageCheck,
    ShieldCheck,
    ShoppingBag,
    Tag,
    Ticket,
    TriangleAlert,
    Truck,
    UserRound,
  } from 'lucide-vue-next'

  import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue'

  import { useRouter } from 'vue-router'

  import { ShopApiError, shopService } from '@/services/shopService'

  import { useCartStore } from '@/stores/cart'

  import type {
    CreateShopOrderRequest,
    ShopDeliveryMethod,
    ShopPricingData,
    ShopPricingItem,
  } from '@/types/shop'

  const router = useRouter()
  const cartStore = useCartStore()

  /*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/

  const form = reactive({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',

    address: '',
    city: '',
    postalCode: '',

    notes: '',
  })

  /*
|--------------------------------------------------------------------------
| Delivery
|--------------------------------------------------------------------------
*/

  const deliveryMethod = ref<ShopDeliveryMethod>('courier')

  /*
|--------------------------------------------------------------------------
| Discount
|--------------------------------------------------------------------------
*/

  type DiscountMode = 'none' | 'season_ticket' | 'voucher'

  const discountMode = ref<DiscountMode>('none')

  const seasonTicketNumber = ref('')
  const voucherCode = ref('')

  /*
|--------------------------------------------------------------------------
| Pricing
|--------------------------------------------------------------------------
*/

  const pricing = ref<ShopPricingData | null>(null)

  const pricingLoading = ref(false)
  const pricingError = ref('')
  const pricingVerified = ref(false)

  let pricingTimer: ReturnType<typeof setTimeout> | null = null

  let pricingRequestId = 0

  /*
|--------------------------------------------------------------------------
| Order
|--------------------------------------------------------------------------
*/

  const submitting = ref(false)
  const submitError = ref('')

  const validationErrors = ref<Record<string, string[]>>({})

  /*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

  const requiresAddress = computed(() => deliveryMethod.value === 'courier')

  const canSubmit = computed(() => {
    return (
      !cartStore.isEmpty &&
      pricingVerified.value &&
      pricing.value !== null &&
      !pricingLoading.value &&
      !pricingError.value &&
      !submitting.value
    )
  })

  const activeSeasonTicket = computed(() => pricing.value?.season_ticket ?? null)

  const activeVoucher = computed(() => pricing.value?.voucher ?? null)

  const hasDiscount = computed(() => pricing.value !== null && pricing.value.discount_amount > 0)

  /*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

  function formatPrice(value: number) {
    return `${Number(value).toFixed(2).replace('.', ',')} KM`
  }

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

  function getSeasonTicketValue() {
    if (discountMode.value !== 'season_ticket') {
      return null
    }

    const value = seasonTicketNumber.value.trim()

    return value !== '' ? value : null
  }

  function getVoucherValue() {
    if (discountMode.value !== 'voucher') {
      return null
    }

    const value = voucherCode.value.trim()

    return value !== '' ? value.toUpperCase() : null
  }

  /*
|--------------------------------------------------------------------------
| Pricing
|--------------------------------------------------------------------------
*/

  async function calculatePricing() {
    if (cartStore.isEmpty) {
      pricing.value = null
      pricingVerified.value = false
      pricingError.value = ''

      return
    }

    const currentRequestId = ++pricingRequestId

    pricingLoading.value = true
    pricingVerified.value = false
    pricingError.value = ''

    try {
      const response = await shopService.calculatePricing({
        items: getPricingItems(),

        season_ticket_number: getSeasonTicketValue(),

        voucher_code: getVoucherValue(),

        delivery_method: deliveryMethod.value,
      })

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
        pricingError.value = 'Nije moguće izračunati narudžbu. Pokušajte ponovo.'
      }
    } finally {
      if (currentRequestId === pricingRequestId) {
        pricingLoading.value = false
      }
    }
  }

  function schedulePricing() {
    pricingVerified.value = false

    if (pricingTimer) {
      clearTimeout(pricingTimer)
    }

    pricingTimer = setTimeout(() => {
      void calculatePricing()
    }, 450)
  }

  /*
|--------------------------------------------------------------------------
| Discount selection
|--------------------------------------------------------------------------
*/

  function selectDiscountMode(mode: DiscountMode) {
    discountMode.value = mode

    if (mode !== 'season_ticket') {
      seasonTicketNumber.value = ''
    }

    if (mode !== 'voucher') {
      voucherCode.value = ''
    }

    schedulePricing()
  }

  /*
|--------------------------------------------------------------------------
| Client validation
|--------------------------------------------------------------------------
*/

  function validateForm(): boolean {
    validationErrors.value = {}

    const errors: Record<string, string[]> = {}

    if (!form.firstName.trim()) {
      errors.first_name = ['Ime je obavezno.']
    }

    if (!form.lastName.trim()) {
      errors.last_name = ['Prezime je obavezno.']
    }

    if (!form.email.trim()) {
      errors.email = ['Email adresa je obavezna.']
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

      if (!emailRegex.test(form.email)) {
        errors.email = ['Email adresa nije ispravna.']
      }
    }

    if (!form.phone.trim()) {
      errors.phone = ['Broj telefona je obavezan.']
    }

    if (requiresAddress.value) {
      if (!form.address.trim()) {
        errors.address = ['Adresa je obavezna za dostavu.']
      }

      if (!form.city.trim()) {
        errors.city = ['Grad je obavezan za dostavu.']
      }
    }

    validationErrors.value = errors

    return Object.keys(errors).length === 0
  }

  function fieldError(field: string) {
    return validationErrors.value[field]?.[0]
  }

  /*
|--------------------------------------------------------------------------
| Order items
|--------------------------------------------------------------------------
*/

  function getOrderItems() {
    return cartStore.items.map(item => ({
      variant_id: item.variantId,

      quantity: item.quantity,

      customization: item.customization
        ? {
            name: item.customization.name || undefined,

            number: item.customization.number || undefined,
          }
        : null,
    }))
  }

  /*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

  async function submitOrder() {
    if (submitting.value) {
      return
    }

    submitError.value = ''

    if (cartStore.isEmpty) {
      submitError.value = 'Korpa je prazna.'

      return
    }

    if (!validateForm()) {
      submitError.value = 'Provjerite unesene podatke.'

      window.scrollTo({
        top: 0,
        behavior: 'smooth',
      })

      return
    }

    /*
     * Neposredno prije ordera ponovo
     * potvrđujemo pricing.
     */
    await calculatePricing()

    if (!pricingVerified.value || !pricing.value) {
      submitError.value = pricingError.value || 'Narudžbu trenutno nije moguće potvrditi.'

      return
    }

    submitting.value = true

    try {
      const payload: CreateShopOrderRequest = {
        first_name: form.firstName.trim(),

        last_name: form.lastName.trim(),

        email: form.email.trim(),

        phone: form.phone.trim(),

        address: requiresAddress.value ? form.address.trim() : null,

        city: requiresAddress.value ? form.city.trim() : null,

        postal_code:
          requiresAddress.value && form.postalCode.trim() ? form.postalCode.trim() : null,

        delivery_method: deliveryMethod.value,

        notes: form.notes.trim() ? form.notes.trim() : null,

        season_ticket_number: getSeasonTicketValue(),

        voucher_code: getVoucherValue(),

        items: getOrderItems(),
      }

      const response = await shopService.createOrder(payload)

      const orderData = response.data

      /*
       * Tek nakon HTTP 201 / success=true
       * čistimo korpu.
       */
      cartStore.clearCart()

      await router.replace({
        name: 'ShopOrderSuccess',

        query: {
          order: orderData.order_number,

          total: String(orderData.total),

          delivery: orderData.delivery_method,
        },
      })
    } catch (error) {
      if (error instanceof ShopApiError) {
        submitError.value = error.message

        validationErrors.value = error.errors
      } else {
        submitError.value = 'Došlo je do greške prilikom slanja narudžbe. Pokušajte ponovo.'
      }
    } finally {
      submitting.value = false
    }
  }

  /*
|--------------------------------------------------------------------------
| Watch pricing dependencies
|--------------------------------------------------------------------------
*/

  watch(deliveryMethod, () => {
    schedulePricing()
  })

  watch(seasonTicketNumber, () => {
    if (discountMode.value === 'season_ticket') {
      schedulePricing()
    }
  })

  watch(voucherCode, () => {
    if (discountMode.value === 'voucher') {
      schedulePricing()
    }
  })

  watch(
    () =>
      cartStore.items.map(item => ({
        key: item.key,
        variantId: item.variantId,
        quantity: item.quantity,
      })),
    () => {
      schedulePricing()
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

    pricingRequestId++
  })
</script>

<template>
  <div class="checkout-page min-h-screen bg-[#031426]">
    <!-- EMPTY -->
    <div
      v-if="cartStore.isEmpty"
      class="mx-auto flex min-h-[70vh] max-w-2xl items-center justify-center px-4 py-16 text-center"
    >
      <div>
        <div
          class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/10 text-[#edc06a]"
        >
          <ShoppingBag :size="27" />
        </div>

        <h1 class="checkout-heading mt-6 text-4xl font-black uppercase text-white">
          Korpa je prazna
        </h1>

        <p class="mt-3 text-sm text-white/50">Dodaj proizvod prije nastavka na naplatu.</p>

        <RouterLink
          to="/shop"
          class="mt-7 inline-flex h-12 items-center justify-center bg-[#edc06a] px-7 text-[10px] font-black uppercase tracking-[0.1em] text-[#031426]"
        >
          Nazad u webshop
        </RouterLink>
      </div>
    </div>

    <!-- CHECKOUT -->
    <template v-else>
      <!-- HEADER -->
      <section class="border-b border-white/10">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-10">
          <RouterLink
            to="/shop/cart"
            class="inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.1em] text-white/50 transition hover:text-[#edc06a]"
          >
            <ArrowLeft :size="13" />
            Nazad na korpu
          </RouterLink>

          <div class="mt-7">
            <div
              class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.14em] text-[#edc06a]"
            >
              <ShoppingBag :size="14" />
              FK Radnik Shop
            </div>

            <h1
              class="checkout-heading mt-2 text-[42px] font-black uppercase leading-none tracking-[-0.025em] text-white sm:text-[56px]"
            >
              Završi narudžbu
            </h1>

            <p class="mt-3 max-w-xl text-xs leading-5 text-white/45">
              Unesite podatke za narudžbu, odaberite način preuzimanja i eventualni popust.
            </p>
          </div>
        </div>
      </section>

      <main
        class="mx-auto grid max-w-7xl items-start gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_390px] lg:py-12 xl:grid-cols-[1fr_420px]"
      >
        <!-- LEFT -->
        <div class="grid gap-6">
          <!-- SUBMIT ERROR -->
          <div
            v-if="submitError"
            class="flex items-start gap-3 rounded-lg border border-red-400/20 bg-red-500/10 px-4 py-4"
          >
            <TriangleAlert :size="18" class="mt-0.5 flex-shrink-0 text-red-400" />

            <div>
              <p class="text-[10px] font-black uppercase tracking-[0.08em] text-red-300">
                Narudžba nije završena
              </p>

              <p class="mt-1 text-xs leading-5 text-red-200/80">
                {{ submitError }}
              </p>
            </div>
          </div>

          <!-- CUSTOMER -->
          <section class="rounded-xl border border-white/10 bg-[#072846] p-5 sm:p-7">
            <div class="mb-6 flex items-center gap-3">
              <div
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#006faf] text-white"
              >
                <UserRound :size="17" />
              </div>

              <div>
                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#edc06a]">
                  Korak 01
                </p>

                <h2 class="checkout-heading text-2xl font-black uppercase text-white">
                  Podaci kupca
                </h2>
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <!-- FIRST NAME -->
              <label class="block">
                <span class="checkout-label"> Ime * </span>

                <input
                  v-model="form.firstName"
                  type="text"
                  maxlength="100"
                  autocomplete="given-name"
                  class="checkout-input"
                  :class="{
                    'checkout-input-error': fieldError('first_name'),
                  }"
                />

                <span v-if="fieldError('first_name')" class="checkout-error">
                  {{ fieldError('first_name') }}
                </span>
              </label>

              <!-- LAST NAME -->
              <label class="block">
                <span class="checkout-label"> Prezime * </span>

                <input
                  v-model="form.lastName"
                  type="text"
                  maxlength="100"
                  autocomplete="family-name"
                  class="checkout-input"
                  :class="{
                    'checkout-input-error': fieldError('last_name'),
                  }"
                />

                <span v-if="fieldError('last_name')" class="checkout-error">
                  {{ fieldError('last_name') }}
                </span>
              </label>

              <!-- EMAIL -->
              <label class="block">
                <span class="checkout-label"> Email * </span>

                <input
                  v-model="form.email"
                  type="email"
                  maxlength="255"
                  autocomplete="email"
                  class="checkout-input"
                  :class="{
                    'checkout-input-error': fieldError('email'),
                  }"
                />

                <span v-if="fieldError('email')" class="checkout-error">
                  {{ fieldError('email') }}
                </span>
              </label>

              <!-- PHONE -->
              <label class="block">
                <span class="checkout-label"> Telefon * </span>

                <input
                  v-model="form.phone"
                  type="tel"
                  maxlength="50"
                  autocomplete="tel"
                  placeholder="+387"
                  class="checkout-input"
                  :class="{
                    'checkout-input-error': fieldError('phone'),
                  }"
                />

                <span v-if="fieldError('phone')" class="checkout-error">
                  {{ fieldError('phone') }}
                </span>
              </label>
            </div>
          </section>

          <!-- DELIVERY -->
          <section class="rounded-xl border border-white/10 bg-[#072846] p-5 sm:p-7">
            <div class="mb-6 flex items-center gap-3">
              <div
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#006faf] text-white"
              >
                <Truck :size="17" />
              </div>

              <div>
                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#edc06a]">
                  Korak 02
                </p>

                <h2 class="checkout-heading text-2xl font-black uppercase text-white">
                  Preuzimanje
                </h2>
              </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
              <!-- COURIER -->
              <button
                type="button"
                class="checkout-choice"
                :class="{
                  'checkout-choice-active': deliveryMethod === 'courier',
                }"
                @click="deliveryMethod = 'courier'"
              >
                <div class="flex items-center justify-between">
                  <Truck :size="20" />

                  <span
                    v-if="deliveryMethod === 'courier'"
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-[#edc06a] text-[#031426]"
                  >
                    <Check :size="12" />
                  </span>
                </div>

                <strong class="mt-4 block text-sm uppercase"> Kurirska dostava </strong>

                <span class="mt-1 block text-[10px] leading-4 text-white/45">
                  Dostava na unesenu adresu.
                </span>
              </button>

              <!-- PICKUP -->
              <button
                type="button"
                class="checkout-choice"
                :class="{
                  'checkout-choice-active': deliveryMethod === 'pickup',
                }"
                @click="deliveryMethod = 'pickup'"
              >
                <div class="flex items-center justify-between">
                  <MapPin :size="20" />

                  <span
                    v-if="deliveryMethod === 'pickup'"
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-[#edc06a] text-[#031426]"
                  >
                    <Check :size="12" />
                  </span>
                </div>

                <strong class="mt-4 block text-sm uppercase"> Lično preuzimanje </strong>

                <span class="mt-1 block text-[10px] leading-4 text-white/45">
                  Bez troškova dostave.
                </span>
              </button>
            </div>

            <!-- ADDRESS -->
            <Transition name="address">
              <div
                v-if="requiresAddress"
                class="mt-6 grid gap-4 border-t border-white/10 pt-6 sm:grid-cols-2"
              >
                <label class="block sm:col-span-2">
                  <span class="checkout-label"> Adresa * </span>

                  <input
                    v-model="form.address"
                    type="text"
                    maxlength="255"
                    autocomplete="street-address"
                    class="checkout-input"
                    :class="{
                      'checkout-input-error': fieldError('address'),
                    }"
                  />

                  <span v-if="fieldError('address')" class="checkout-error">
                    {{ fieldError('address') }}
                  </span>
                </label>

                <label class="block">
                  <span class="checkout-label"> Grad * </span>

                  <input
                    v-model="form.city"
                    type="text"
                    maxlength="100"
                    autocomplete="address-level2"
                    class="checkout-input"
                    :class="{
                      'checkout-input-error': fieldError('city'),
                    }"
                  />

                  <span v-if="fieldError('city')" class="checkout-error">
                    {{ fieldError('city') }}
                  </span>
                </label>

                <label class="block">
                  <span class="checkout-label"> Poštanski broj </span>

                  <input
                    v-model="form.postalCode"
                    type="text"
                    maxlength="20"
                    autocomplete="postal-code"
                    class="checkout-input"
                  />
                </label>
              </div>
            </Transition>
          </section>

          <!-- DISCOUNT -->
          <section class="rounded-xl border border-white/10 bg-[#072846] p-5 sm:p-7">
            <div class="mb-6 flex items-center gap-3">
              <div
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#006faf] text-white"
              >
                <BadgePercent :size="17" />
              </div>

              <div>
                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#edc06a]">
                  Korak 03
                </p>

                <h2 class="checkout-heading text-2xl font-black uppercase text-white">Popust</h2>
              </div>
            </div>

            <p class="mb-5 text-[10px] leading-5 text-white/45">
              Sezonska karta i promo vaučer ne mogu se kombinovati.
            </p>

            <div class="grid gap-3 sm:grid-cols-3">
              <button
                type="button"
                class="discount-choice"
                :class="{
                  'discount-choice-active': discountMode === 'none',
                }"
                @click="selectDiscountMode('none')"
              >
                Bez popusta
              </button>

              <button
                type="button"
                class="discount-choice"
                :class="{
                  'discount-choice-active': discountMode === 'season_ticket',
                }"
                @click="selectDiscountMode('season_ticket')"
              >
                <Ticket :size="14" />
                Sezonska
              </button>

              <button
                type="button"
                class="discount-choice"
                :class="{
                  'discount-choice-active': discountMode === 'voucher',
                }"
                @click="selectDiscountMode('voucher')"
              >
                <Tag :size="14" />
                Promo kod
              </button>
            </div>

            <!-- SEASON TICKET -->
            <div v-if="discountMode === 'season_ticket'" class="mt-5">
              <label class="block">
                <span class="checkout-label"> Broj sezonske karte </span>

                <input
                  v-model="seasonTicketNumber"
                  type="text"
                  maxlength="100"
                  placeholder="Unesite broj sezonske karte"
                  class="checkout-input"
                />
              </label>

              <div
                v-if="activeSeasonTicket"
                class="mt-3 flex items-center gap-2 rounded bg-emerald-500/10 px-3 py-2.5"
              >
                <CheckCircle2 :size="15" class="text-emerald-400" />

                <span class="text-[10px] font-bold text-emerald-200">
                  Sezonska karta
                  {{ activeSeasonTicket.ticket_number }}
                  je prihvaćena.
                </span>
              </div>
            </div>

            <!-- VOUCHER -->
            <div v-if="discountMode === 'voucher'" class="mt-5">
              <label class="block">
                <span class="checkout-label"> Promo kod </span>

                <input
                  v-model="voucherCode"
                  type="text"
                  maxlength="100"
                  placeholder="Unesite promo kod"
                  class="checkout-input uppercase"
                />
              </label>

              <div
                v-if="activeVoucher"
                class="mt-3 flex items-center gap-2 rounded bg-emerald-500/10 px-3 py-2.5"
              >
                <CheckCircle2 :size="15" class="text-emerald-400" />

                <span class="text-[10px] font-bold text-emerald-200">
                  {{ activeVoucher.name }}
                  —
                  {{ activeVoucher.code }}
                </span>
              </div>
            </div>

            <!-- PRICING ERROR -->
            <div
              v-if="pricingError"
              class="mt-4 flex items-start gap-2 rounded bg-red-500/10 px-3 py-3"
            >
              <TriangleAlert :size="15" class="mt-0.5 flex-shrink-0 text-red-400" />

              <span class="text-[10px] leading-4 text-red-200">
                {{ pricingError }}
              </span>
            </div>
          </section>

          <!-- NOTES -->
          <section class="rounded-xl border border-white/10 bg-[#072846] p-5 sm:p-7">
            <div class="mb-5 flex items-center gap-3">
              <div
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#006faf] text-white"
              >
                <PackageCheck :size="17" />
              </div>

              <div>
                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#edc06a]">
                  Napomena
                </p>

                <h2 class="checkout-heading text-2xl font-black uppercase text-white">
                  Dodatne informacije
                </h2>
              </div>
            </div>

            <textarea
              v-model="form.notes"
              maxlength="2000"
              rows="4"
              placeholder="Napomena uz narudžbu..."
              class="checkout-input resize-none"
            />
          </section>

          <!-- PAYMENT -->
          <section class="rounded-xl border border-[#edc06a]/30 bg-[#edc06a]/[0.06] p-5 sm:p-6">
            <div class="flex items-start gap-4">
              <div
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-[#edc06a] text-[#031426]"
              >
                <CreditCard :size="18" />
              </div>

              <div>
                <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#edc06a]">
                  Način plaćanja
                </p>

                <h3 class="checkout-heading mt-1 text-xl font-black uppercase text-white">
                  Plaćanje pouzećem
                </h3>

                <p class="mt-2 text-[10px] leading-5 text-white/45">
                  Narudžbu plaćate prilikom preuzimanja.
                </p>
              </div>
            </div>
          </section>
        </div>

        <!-- RIGHT SUMMARY -->
        <aside class="lg:sticky lg:top-[105px]">
          <div class="overflow-hidden rounded-xl border border-white/10 bg-[#072846] shadow-2xl">
            <div class="border-b border-white/10 px-5 py-5 sm:px-6">
              <p class="text-[9px] font-black uppercase tracking-[0.14em] text-[#edc06a]">
                Vaša narudžba
              </p>

              <h2 class="checkout-heading mt-1 text-2xl font-black uppercase text-white">
                Pregled
              </h2>
            </div>

            <!-- ITEMS -->
            <div class="max-h-[310px] overflow-y-auto px-5 sm:px-6">
              <div
                v-for="item in cartStore.items"
                :key="item.key"
                class="flex gap-3 border-b border-white/10 py-4 last:border-0"
              >
                <div class="h-16 w-14 flex-shrink-0 overflow-hidden rounded bg-white">
                  <img
                    v-if="item.image"
                    :src="item.image"
                    :alt="item.name"
                    class="h-full w-full object-contain p-1"
                  />

                  <div v-else class="flex h-full items-center justify-center">
                    <ShoppingBag :size="17" class="text-[#006faf]" />
                  </div>
                </div>

                <div class="min-w-0 flex-1">
                  <p
                    class="checkout-heading line-clamp-2 text-sm font-black uppercase leading-[1.05] text-white"
                  >
                    {{ item.name }}
                  </p>

                  <p class="mt-1 text-[9px] text-white/40">
                    Veličina:
                    {{ item.variantSize }}
                    ·
                    {{ item.quantity }}
                    kom.
                  </p>

                  <p v-if="item.customization" class="mt-1 text-[9px] text-[#edc06a]/80">
                    <template v-if="item.customization.name">
                      {{ item.customization.name }}
                    </template>

                    <template v-if="item.customization.number">
                      /
                      {{ item.customization.number }}
                    </template>
                  </p>
                </div>

                <div class="flex-shrink-0 text-right text-[11px] font-black text-white">
                  {{ formatPrice(item.unitPrice * item.quantity) }}
                </div>
              </div>
            </div>

            <!-- TOTALS -->
            <div class="border-t border-white/10 px-5 py-5 sm:px-6">
              <div
                v-if="pricingLoading"
                class="mb-5 flex items-center gap-2 rounded bg-white/5 px-3 py-2.5"
              >
                <LoaderCircle :size="14" class="animate-spin text-[#edc06a]" />

                <span class="text-[9px] text-white/50"> Obračun cijene... </span>
              </div>

              <div
                v-else-if="pricingVerified"
                class="mb-5 flex items-center gap-2 rounded bg-emerald-500/10 px-3 py-2.5"
              >
                <ShieldCheck :size="14" class="text-emerald-400" />

                <span class="text-[9px] font-semibold text-emerald-200">
                  Cijena i stanje potvrđeni
                </span>
              </div>

              <div class="grid gap-3 text-xs">
                <div class="flex justify-between gap-4">
                  <span class="text-white/45"> Međuzbir </span>

                  <strong class="text-white">
                    {{ formatPrice(pricing?.subtotal ?? cartStore.subtotal) }}
                  </strong>
                </div>

                <div v-if="hasDiscount" class="flex justify-between gap-4">
                  <span class="text-white/45">
                    Popust

                    <template v-if="pricing?.discount_percent">
                      ({{ pricing.discount_percent }}%)
                    </template>
                  </span>

                  <strong class="text-emerald-400">
                    -
                    {{ formatPrice(pricing?.discount_amount ?? 0) }}
                  </strong>
                </div>

                <div class="flex justify-between gap-4">
                  <span class="text-white/45"> Dostava </span>

                  <strong class="text-white">
                    <template v-if="pricing && pricing.shipping_amount > 0">
                      {{ formatPrice(pricing.shipping_amount) }}
                    </template>

                    <template v-else> 0,00 KM </template>
                  </strong>
                </div>
              </div>

              <div class="my-5 border-t border-white/10" />

              <div class="flex items-end justify-between gap-4">
                <div>
                  <p class="text-[9px] font-black uppercase tracking-[0.1em] text-white/40">
                    Za plaćanje
                  </p>

                  <p class="checkout-heading text-xl font-black uppercase text-white">Ukupno</p>
                </div>

                <div class="text-[28px] font-black leading-none text-[#edc06a]">
                  {{ formatPrice(pricing?.total ?? cartStore.subtotal) }}
                </div>
              </div>

              <!-- SUBMIT -->
              <button
                type="button"
                :disabled="!canSubmit"
                class="mt-6 flex h-[54px] w-full items-center justify-center gap-2 bg-[#edc06a] px-5 text-[10px] font-black uppercase tracking-[0.1em] text-[#031426] transition hover:bg-[#f5cd7c] disabled:cursor-not-allowed disabled:opacity-40"
                @click="submitOrder"
              >
                <LoaderCircle v-if="submitting" :size="16" class="animate-spin" />

                <ShoppingBag v-else :size="16" />

                {{ submitting ? 'Šaljem narudžbu...' : 'Potvrdi narudžbu' }}
              </button>

              <p class="mt-3 text-center text-[9px] leading-4 text-white/35">
                Klikom potvrđujete narudžbu sa obavezom plaćanja prilikom preuzimanja.
              </p>
            </div>
          </div>
        </aside>
      </main>
    </template>
  </div>
</template>

<style scoped>
  .checkout-page {
    font-family:
      'Hanken Grotesk',
      system-ui,
      -apple-system,
      BlinkMacSystemFont,
      'Segoe UI',
      sans-serif;
  }

  .checkout-heading {
    font-family: 'Archivo Narrow', 'Arial Narrow', Arial, sans-serif;
  }

  .checkout-label {
    display: block;
    margin-bottom: 7px;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgb(255 255 255 / 0.55);
  }

  .checkout-input {
    width: 100%;
    min-height: 46px;
    border: 1px solid rgb(255 255 255 / 0.12);
    border-radius: 6px;
    background: rgb(3 20 38 / 0.7);
    padding: 0 13px;
    color: white;
    font-size: 13px;
    outline: none;
    transition:
      border-color 180ms ease,
      box-shadow 180ms ease,
      background 180ms ease;
  }

  textarea.checkout-input {
    padding-top: 12px;
    padding-bottom: 12px;
  }

  .checkout-input::placeholder {
    color: rgb(255 255 255 / 0.25);
  }

  .checkout-input:focus {
    border-color: #006faf;
    background: #031426;
    box-shadow: 0 0 0 3px rgb(0 111 175 / 0.13);
  }

  .checkout-input-error {
    border-color: rgb(248 113 113 / 0.7);
  }

  .checkout-error {
    display: block;
    margin-top: 5px;
    font-size: 9px;
    color: #fca5a5;
  }

  .checkout-choice {
    min-height: 125px;
    border: 1px solid rgb(255 255 255 / 0.1);
    border-radius: 8px;
    background: rgb(3 20 38 / 0.55);
    padding: 16px;
    text-align: left;
    color: rgb(255 255 255 / 0.7);
    transition:
      border-color 180ms ease,
      background 180ms ease,
      transform 180ms ease;
  }

  .checkout-choice:hover {
    border-color: rgb(237 192 106 / 0.35);
    transform: translateY(-1px);
  }

  .checkout-choice-active {
    border-color: #edc06a;
    background: rgb(237 192 106 / 0.08);
    color: white;
  }

  .discount-choice {
    display: flex;
    min-height: 42px;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1px solid rgb(255 255 255 / 0.1);
    border-radius: 6px;
    background: rgb(3 20 38 / 0.55);
    padding: 0 10px;
    color: rgb(255 255 255 / 0.5);
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    transition:
      border-color 180ms ease,
      background 180ms ease,
      color 180ms ease;
  }

  .discount-choice:hover {
    color: white;
  }

  .discount-choice-active {
    border-color: #edc06a;
    background: rgb(237 192 106 / 0.1);
    color: #edc06a;
  }

  .address-enter-active,
  .address-leave-active {
    transition:
      opacity 200ms ease,
      transform 200ms ease;
  }

  .address-enter-from,
  .address-leave-to {
    opacity: 0;
    transform: translateY(-6px);
  }
</style>
