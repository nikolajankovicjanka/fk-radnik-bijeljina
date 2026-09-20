<script setup lang="ts">
  import { computed, onMounted, ref, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import {
    ArrowLeft,
    Check,
    ChevronRight,
    Minus,
    Plus,
    RotateCcw,
    ShieldCheck,
    ShoppingBag,
    Truck,
  } from 'lucide-vue-next'

  import { shopService } from '@/services/shopService'
  import type { ShopProduct, ShopProductVariant } from '@/types/shop'
  import { getShopImageUrl } from '@/utils/shop'
  import { useCartStore } from '@/stores/cart'

  const route = useRoute()
  const cartStore = useCartStore()

  const product = ref<ShopProduct | null>(null)
  const selectedVariant = ref<ShopProductVariant | null>(null)

  const quantity = ref(1)

  const loading = ref(true)
  const error = ref('')

  const addedToCart = ref(false)

  const wantsCustomization = ref(false)
  const customizationName = ref('')
  const customizationNumber = ref('')

  const slug = computed(() => String(route.params.slug ?? ''))

  /*
|--------------------------------------------------------------------------
| Product images
|--------------------------------------------------------------------------
*/

  const productImage = computed(() => getShopImageUrl(product.value?.main_image))

  const selectedImage = ref<string | null>(null)

  const productGallery = computed(() => {
    if (!product.value) {
      return []
    }

    const gallery: Array<{
      id: string
      url: string
      isMain: boolean
    }> = []

    const mainImage = getShopImageUrl(product.value.main_image)

    if (mainImage) {
      gallery.push({
        id: 'main',
        url: mainImage,
        isMain: true,
      })
    }

    for (const image of product.value.images ?? []) {
      const imageUrl = getShopImageUrl(image.image_path)

      if (!imageUrl) {
        continue
      }

      gallery.push({
        id: `gallery-${image.id}`,
        url: imageUrl,
        isMain: false,
      })
    }

    return gallery
  })

  const activeProductImage = computed(() => {
    return selectedImage.value ?? productGallery.value[0]?.url ?? null
  })

  const activeProductImageIndex = computed(() => {
    if (!activeProductImage.value) {
      return 0
    }

    const index = productGallery.value.findIndex(image => image.url === activeProductImage.value)

    return index >= 0 ? index : 0
  })

  /*
|--------------------------------------------------------------------------
| Pricing
|--------------------------------------------------------------------------
*/

  const currentPrice = computed(() => {
    if (!product.value) {
      return '0.00'
    }

    return product.value.sale_price ?? product.value.price
  })

  const hasDiscount = computed(() => {
    if (!product.value?.sale_price) {
      return false
    }

    return Number(product.value.sale_price) < Number(product.value.price)
  })

  /*
|--------------------------------------------------------------------------
| Stock
|--------------------------------------------------------------------------
*/

  const availableQuantity = computed(() => {
    if (!selectedVariant.value) {
      return 0
    }

    return Math.max(
      0,
      selectedVariant.value.stock_quantity - selectedVariant.value.reserved_quantity
    )
  })

  const productAvailable = computed(() => availableQuantity.value > 0)

  /*
|--------------------------------------------------------------------------
| Customization
|--------------------------------------------------------------------------
*/

  const customizationValid = computed(() => {
    if (!wantsCustomization.value) {
      return true
    }

    return customizationName.value.trim().length > 0 && customizationNumber.value.trim().length > 0
  })

  const canAddToCart = computed(() => {
    return (
      !!product.value &&
      !!selectedVariant.value &&
      productAvailable.value &&
      quantity.value > 0 &&
      customizationValid.value
    )
  })

  /*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

  function variantAvailableQuantity(variant: ShopProductVariant) {
    return Math.max(0, variant.stock_quantity - variant.reserved_quantity)
  }

  function formatPrice(price: string) {
    return `${Number(price).toFixed(2).replace('.', ',')} KM`
  }

  function selectVariant(variant: ShopProductVariant) {
    if (variantAvailableQuantity(variant) <= 0) {
      return
    }

    selectedVariant.value = variant
    quantity.value = 1
    addedToCart.value = false
  }

  function selectProductImage(imageUrl: string) {
    selectedImage.value = imageUrl
  }

  function increaseQuantity() {
    if (quantity.value >= availableQuantity.value) {
      return
    }

    quantity.value += 1
  }

  function decreaseQuantity() {
    if (quantity.value <= 1) {
      return
    }

    quantity.value -= 1
  }

  /*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

  function addToCart() {
    if (!product.value || !selectedVariant.value || !canAddToCart.value) {
      return
    }

    cartStore.addItem({
      productId: product.value.id,
      variantId: selectedVariant.value.id,

      slug: product.value.slug,
      name: product.value.name.trim(),
      sku: product.value.sku,

      variantSize: selectedVariant.value.size,
      variantSku: selectedVariant.value.sku,

      // Cart always uses the main product image.
      image: productImage.value,

      unitPrice: Number(currentPrice.value),

      originalPrice: hasDiscount.value ? Number(product.value.price) : null,

      quantity: quantity.value,

      availableQuantity: availableQuantity.value,

      isCustomizable: product.value.is_customizable,

      customization:
        product.value.is_customizable && wantsCustomization.value
          ? {
              name: customizationName.value.trim().toUpperCase(),

              number: customizationNumber.value.trim(),
            }
          : null,
    })

    addedToCart.value = true

    window.setTimeout(() => {
      addedToCart.value = false
    }, 2000)
  }

  /*
|--------------------------------------------------------------------------
| Product loading
|--------------------------------------------------------------------------
*/

  function resetProductState() {
    quantity.value = 1

    wantsCustomization.value = false
    customizationName.value = ''
    customizationNumber.value = ''

    selectedImage.value = null

    addedToCart.value = false
  }

  async function loadProduct() {
    loading.value = true
    error.value = ''

    resetProductState()

    try {
      const response = await shopService.getProduct(slug.value)

      product.value = response.data

      selectedVariant.value =
        response.data.variants.find(variant => variantAvailableQuantity(variant) > 0) ??
        response.data.variants[0] ??
        null
    } catch (e) {
      console.error(e)

      product.value = null
      selectedVariant.value = null

      error.value = 'Proizvod trenutno nije moguće učitati.'
    } finally {
      loading.value = false
    }
  }

  onMounted(loadProduct)

  watch(slug, loadProduct)
</script>

<template>
  <div class="product-page min-h-screen bg-[#F4F7F9]">
    <!-- =====================================================
         BREADCRUMB
         ===================================================== -->
    <div class="border-b border-[#DCEAF4] bg-white">
      <div
        class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.08em] text-[#8e9197] sm:px-6"
      >
        <RouterLink to="/shop" class="transition hover:text-[#006faf]"> Webshop </RouterLink>

        <ChevronRight :size="12" />

        <span v-if="product" class="max-w-[220px] truncate text-[#072846]">
          {{ product.name.trim() }}
        </span>
      </div>
    </div>

    <main class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:py-12">
      <!-- BACK -->
      <RouterLink
        to="/shop"
        class="mb-7 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.1em] text-[#072846] transition hover:text-[#006faf]"
      >
        <ArrowLeft :size="15" />

        Nazad na proizvode
      </RouterLink>

      <!-- =================================================
           LOADING
           ================================================= -->
      <div v-if="loading" class="grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:gap-14">
        <div class="aspect-[4/5] animate-pulse rounded-lg bg-[#DCEAF4] lg:aspect-square" />

        <div class="space-y-5 pt-4">
          <div class="h-3 w-24 animate-pulse bg-slate-200" />
          <div class="h-14 w-4/5 animate-pulse bg-slate-200" />
          <div class="h-8 w-32 animate-pulse bg-slate-200" />
          <div class="h-20 w-full animate-pulse bg-slate-200" />
        </div>
      </div>

      <!-- =================================================
           ERROR
           ================================================= -->
      <div
        v-else-if="error || !product"
        class="rounded-lg border border-[#DCEAF4] bg-white px-6 py-16 text-center"
      >
        <p class="font-semibold text-[#031426]">
          {{ error }}
        </p>

        <button
          type="button"
          class="mt-5 bg-[#031426] px-6 py-3 text-xs font-black uppercase tracking-wider text-white transition hover:bg-[#006faf]"
          @click="loadProduct"
        >
          Pokušaj ponovo
        </button>
      </div>

      <!-- =================================================
           PRODUCT
           ================================================= -->
      <div v-else class="grid items-start gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:gap-14">
        <!-- ===============================================
             PRODUCT IMAGE / GALLERY
             =============================================== -->
        <section>
          <div
            :class="[
              'grid gap-3',
              productGallery.length > 1 ? 'md:grid-cols-[76px_minmax(0,1fr)]' : '',
            ]"
          >
            <!-- DESKTOP THUMBNAILS -->
            <div
              v-if="productGallery.length > 1"
              class="order-2 hidden flex-col gap-3 md:order-1 md:flex"
            >
              <button
                v-for="image in productGallery"
                :key="image.id"
                type="button"
                class="relative aspect-square w-[76px] overflow-hidden rounded-md border bg-white transition-all duration-200"
                :class="
                  activeProductImage === image.url
                    ? 'border-[#006faf] ring-1 ring-[#006faf]'
                    : 'border-[#DCEAF4] hover:border-[#006faf]/60'
                "
                :aria-label="`Prikaži sliku proizvoda ${product.name.trim()}`"
                @click="selectProductImage(image.url)"
              >
                <img
                  :src="image.url"
                  :alt="product.name.trim()"
                  class="h-full w-full object-contain p-2"
                />

                <span
                  v-if="activeProductImage === image.url"
                  class="absolute inset-x-0 bottom-0 h-1 bg-[#edc06a]"
                />
              </button>
            </div>

            <!-- MAIN IMAGE -->
            <div
              class="group relative order-1 aspect-[4/5] overflow-hidden rounded-lg border border-[#DCEAF4] bg-white md:order-2 lg:aspect-square"
            >
              <Transition name="product-image" mode="out-in">
                <img
                  v-if="activeProductImage"
                  :key="activeProductImage"
                  :src="activeProductImage"
                  :alt="product.name.trim()"
                  class="h-full w-full object-contain p-5 transition-transform duration-700 ease-out group-hover:scale-[1.02] sm:p-8"
                />

                <div
                  v-else
                  key="product-placeholder"
                  class="flex h-full items-center justify-center bg-gradient-to-b from-[#f8fafb] to-[#edf3f7]"
                >
                  <img
                    src="/logo/FK_Radnik_logo.png"
                    :alt="product.name.trim()"
                    class="w-40 opacity-[0.12] grayscale"
                  />
                </div>
              </Transition>

              <!-- BADGES -->
              <div class="absolute left-4 top-4 flex flex-col items-start gap-2 sm:left-5 sm:top-5">
                <span
                  v-if="product.is_new"
                  class="rounded-sm bg-[#edc06a] px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.12em] text-[#031426]"
                >
                  Novo
                </span>

                <span
                  v-if="product.is_bestseller"
                  class="rounded-sm bg-[#031426] px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.1em] text-white"
                >
                  Bestseller
                </span>

                <span
                  v-if="hasDiscount"
                  class="rounded-sm bg-[#006faf] px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.1em] text-white"
                >
                  Akcija
                </span>
              </div>

              <!-- IMAGE COUNTER -->
              <div
                v-if="productGallery.length > 1"
                class="absolute bottom-4 right-4 rounded-full bg-[#031426]/85 px-3 py-1.5 text-[9px] font-black tracking-[0.08em] text-white backdrop-blur-sm"
              >
                {{ activeProductImageIndex + 1 }}
                /
                {{ productGallery.length }}
              </div>
            </div>
          </div>

          <!-- MOBILE THUMBNAILS -->
          <div
            v-if="productGallery.length > 1"
            class="mt-3 flex gap-2 overflow-x-auto pb-1 md:hidden"
          >
            <button
              v-for="image in productGallery"
              :key="image.id"
              type="button"
              class="relative aspect-square w-[68px] flex-none overflow-hidden rounded-md border bg-white transition-all duration-200"
              :class="
                activeProductImage === image.url
                  ? 'border-[#006faf] ring-1 ring-[#006faf]'
                  : 'border-[#DCEAF4]'
              "
              :aria-label="`Prikaži sliku proizvoda ${product.name.trim()}`"
              @click="selectProductImage(image.url)"
            >
              <img
                :src="image.url"
                :alt="product.name.trim()"
                class="h-full w-full object-contain p-2"
              />

              <span
                v-if="activeProductImage === image.url"
                class="absolute inset-x-0 bottom-0 h-1 bg-[#edc06a]"
              />
            </button>
          </div>

          <!-- PRODUCT ASSURANCES -->
          <div
            class="mt-4 hidden grid-cols-3 overflow-hidden rounded-lg border border-[#DCEAF4] bg-white md:grid"
          >
            <div class="flex items-center gap-3 border-r border-[#DCEAF4] px-4 py-4">
              <ShieldCheck :size="20" class="flex-shrink-0 text-[#006faf]" />

              <span
                class="text-[10px] font-bold uppercase leading-4 tracking-[0.04em] text-[#072846]"
              >
                Zvanični proizvod
              </span>
            </div>

            <div class="flex items-center gap-3 border-r border-[#DCEAF4] px-4 py-4">
              <Truck :size="20" class="flex-shrink-0 text-[#006faf]" />

              <span
                class="text-[10px] font-bold uppercase leading-4 tracking-[0.04em] text-[#072846]"
              >
                Dostava ili preuzimanje
              </span>
            </div>

            <div class="flex items-center gap-3 px-4 py-4">
              <RotateCcw :size="20" class="flex-shrink-0 text-[#006faf]" />

              <span
                class="text-[10px] font-bold uppercase leading-4 tracking-[0.04em] text-[#072846]"
              >
                Sigurna kupovina
              </span>
            </div>
          </div>
        </section>

        <!-- ===============================================
             PRODUCT INFO
             =============================================== -->
        <section class="rounded-lg border border-[#DCEAF4] bg-white p-5 sm:p-7 lg:p-8">
          <!-- CATEGORY -->
          <span class="text-[10px] font-black uppercase tracking-[0.16em] text-[#006faf]">
            {{ product.category.name }}
          </span>

          <!-- NAME -->
          <h1
            class="product-heading mt-2 text-[36px] font-black uppercase leading-[0.98] tracking-[-0.025em] text-[#031426] sm:text-[46px] lg:text-[52px]"
          >
            {{ product.name.trim() }}
          </h1>

          <!-- SKU -->
          <p class="mt-3 text-[10px] font-medium uppercase tracking-[0.08em] text-[#8e9197]">
            Šifra proizvoda:
            {{ product.sku }}
          </p>

          <!-- PRICE -->
          <div class="mt-6 flex flex-wrap items-baseline gap-3 border-b border-[#DCEAF4] pb-6">
            <span class="text-[28px] font-black leading-none text-[#006faf] sm:text-[32px]">
              {{ formatPrice(currentPrice) }}
            </span>

            <span v-if="hasDiscount" class="text-base font-medium text-[#8e9197] line-through">
              {{ formatPrice(product.price) }}
            </span>
          </div>

          <!-- DESCRIPTION -->
          <p
            v-if="product.description || product.short_description"
            class="mt-6 text-sm leading-6 text-[#62666c] sm:text-[15px] sm:leading-7"
          >
            {{ product.description ?? product.short_description }}
          </p>

          <!-- =============================================
               SIZE
               ============================================= -->
          <div v-if="product.variants.length" class="mt-8">
            <div class="mb-3 flex items-center justify-between gap-4">
              <label class="text-[10px] font-black uppercase tracking-[0.12em] text-[#072846]">
                Odaberi veličinu
              </label>

              <span v-if="selectedVariant" class="text-[10px] font-medium text-[#8e9197]">
                Odabrano:

                <strong class="text-[#072846]">
                  {{ selectedVariant.size }}
                </strong>
              </span>
            </div>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="variant in product.variants"
                :key="variant.id"
                type="button"
                :disabled="variantAvailableQuantity(variant) <= 0"
                :class="[
                  'relative flex h-11 min-w-12 items-center justify-center rounded border px-3 text-xs font-black transition-all duration-200',

                  selectedVariant?.id === variant.id
                    ? 'border-[#006faf] bg-[#006faf] text-white'
                    : 'border-[#DCEAF4] bg-white text-[#072846] hover:border-[#006faf]',

                  variantAvailableQuantity(variant) <= 0
                    ? 'cursor-not-allowed overflow-hidden bg-[#f4f6f7] text-[#b2b5b8] opacity-70'
                    : '',
                ]"
                @click="selectVariant(variant)"
              >
                {{ variant.size }}

                <span
                  v-if="variantAvailableQuantity(variant) <= 0"
                  class="absolute left-1/2 top-1/2 h-px w-[140%] -translate-x-1/2 -translate-y-1/2 -rotate-45 bg-[#b2b5b8]"
                />
              </button>
            </div>
          </div>

          <!-- AVAILABILITY -->
          <div
            v-if="selectedVariant"
            class="mt-4 flex items-center gap-2 text-xs font-semibold"
            :class="productAvailable ? 'text-emerald-700' : 'text-red-600'"
          >
            <span
              v-if="productAvailable"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-100"
            >
              <Check :size="11" :stroke-width="3" />
            </span>

            <span>
              {{
                productAvailable
                  ? 'Proizvod je dostupan'
                  : 'Odabrana veličina trenutno nije dostupna'
              }}
            </span>

            <span
              v-if="productAvailable && availableQuantity <= 5"
              class="font-normal text-[#8e9197]"
            >
              · još {{ availableQuantity }} kom.
            </span>
          </div>

          <!-- =============================================
               CUSTOMIZATION
               ============================================= -->
          <div
            v-if="product.is_customizable"
            class="mt-7 rounded-md border border-[#edc06a]/50 bg-[#edc06a]/[0.07] p-4"
          >
            <div class="text-[10px] font-black uppercase tracking-[0.1em] text-[#8a681f]">
              Personalizacija dresa
            </div>

            <p class="mt-1.5 text-xs leading-5 text-[#6f6040]">
              Odaberi da li želiš ime i broj na dresu.
            </p>

            <!-- OPTIONS -->
            <div class="mt-4 grid gap-3">
              <label
                class="flex cursor-pointer items-center gap-3 text-xs font-semibold text-[#072846]"
              >
                <input
                  v-model="wantsCustomization"
                  :value="false"
                  type="radio"
                  class="h-4 w-4 accent-[#006faf]"
                />

                <span> Bez personalizacije </span>
              </label>

              <label
                class="flex cursor-pointer items-center gap-3 text-xs font-semibold text-[#072846]"
              >
                <input
                  v-model="wantsCustomization"
                  :value="true"
                  type="radio"
                  class="h-4 w-4 accent-[#006faf]"
                />

                <span> Želim personalizaciju </span>
              </label>
            </div>

            <!-- CUSTOMIZATION FIELDS -->
            <div
              v-if="wantsCustomization"
              class="mt-5 grid gap-4 border-t border-[#edc06a]/30 pt-5 sm:grid-cols-[1fr_110px]"
            >
              <div>
                <label
                  for="customization-name"
                  class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-[#8a681f]"
                >
                  Ime na dresu
                </label>

                <input
                  id="customization-name"
                  v-model="customizationName"
                  type="text"
                  maxlength="20"
                  autocomplete="off"
                  placeholder="JANKOVIĆ"
                  class="h-11 w-full rounded border border-[#dcc48e] bg-white px-3 text-sm font-bold uppercase text-[#031426] outline-none transition placeholder:text-[#b6a98e] focus:border-[#006faf]"
                />

                <p class="mt-1.5 text-[9px] text-[#8e7c59]">Maksimalno 20 znakova.</p>
              </div>

              <div>
                <label
                  for="customization-number"
                  class="mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-[#8a681f]"
                >
                  Broj
                </label>

                <input
                  id="customization-number"
                  v-model="customizationNumber"
                  type="text"
                  inputmode="numeric"
                  maxlength="2"
                  autocomplete="off"
                  placeholder="10"
                  class="h-11 w-full rounded border border-[#dcc48e] bg-white px-3 text-center text-sm font-black text-[#031426] outline-none transition placeholder:text-[#b6a98e] focus:border-[#006faf]"
                  @input="customizationNumber = customizationNumber.replace(/\D/g, '').slice(0, 2)"
                />
              </div>
            </div>

            <!-- VALIDATION -->
            <p
              v-if="wantsCustomization && !customizationValid"
              class="mt-3 text-[10px] font-semibold text-[#a76513]"
            >
              Unesi ime i broj prije dodavanja u korpu.
            </p>
          </div>

          <!-- =============================================
               QUANTITY
               ============================================= -->
          <div class="mt-7 border-t border-[#DCEAF4] pt-6">
            <label
              class="mb-3 block text-[10px] font-black uppercase tracking-[0.12em] text-[#072846]"
            >
              Količina
            </label>

            <div
              class="flex h-11 w-[132px] overflow-hidden rounded border border-[#DCEAF4] bg-white"
            >
              <button
                type="button"
                :disabled="quantity <= 1"
                aria-label="Smanji količinu"
                class="flex w-11 items-center justify-center text-[#072846] transition hover:bg-[#F4F7F9] disabled:cursor-not-allowed disabled:opacity-30"
                @click="decreaseQuantity"
              >
                <Minus :size="14" />
              </button>

              <div
                class="flex flex-1 items-center justify-center border-x border-[#DCEAF4] text-xs font-black text-[#031426]"
              >
                {{ quantity }}
              </div>

              <button
                type="button"
                :disabled="!productAvailable || quantity >= availableQuantity"
                aria-label="Povećaj količinu"
                class="flex w-11 items-center justify-center text-[#072846] transition hover:bg-[#F4F7F9] disabled:cursor-not-allowed disabled:opacity-30"
                @click="increaseQuantity"
              >
                <Plus :size="14" />
              </button>
            </div>
          </div>

          <!-- =============================================
               ADD TO CART
               ============================================= -->
          <button
            type="button"
            :disabled="!canAddToCart"
            class="mt-5 flex h-[54px] w-full items-center justify-center gap-3 rounded bg-[#006faf] px-6 text-xs font-black uppercase tracking-[0.12em] text-white transition-all duration-200 hover:bg-[#005d94] disabled:cursor-not-allowed disabled:bg-[#9daab2] disabled:opacity-60"
            @click="addToCart"
          >
            <Check v-if="addedToCart" :size="18" :stroke-width="2.5" />

            <ShoppingBag v-else :size="18" />

            {{ addedToCart ? 'Dodano u korpu' : 'Dodaj u korpu' }}
          </button>

          <!-- CART INFO -->
          <div
            v-if="addedToCart"
            class="mt-3 flex items-center justify-between gap-4 rounded bg-emerald-50 px-4 py-3"
          >
            <span class="text-[11px] font-semibold text-emerald-700">
              Proizvod je uspješno dodan u korpu.
            </span>

            <span
              class="whitespace-nowrap text-[10px] font-black uppercase tracking-[0.08em] text-emerald-800"
            >
              {{ cartStore.totalItems }}
              {{ cartStore.totalItems === 1 ? 'artikl' : 'artikala' }}
            </span>
          </div>

          <!-- MOBILE ASSURANCES -->
          <div class="mt-7 grid gap-3 border-t border-[#DCEAF4] pt-6 md:hidden">
            <div class="flex items-center gap-3">
              <ShieldCheck :size="18" class="text-[#006faf]" />

              <span class="text-xs text-[#62666c]"> Zvanični proizvod FK Radnik Bijeljina </span>
            </div>

            <div class="flex items-center gap-3">
              <Truck :size="18" class="text-[#006faf]" />

              <span class="text-xs text-[#62666c]"> Dostava ili lično preuzimanje </span>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<style scoped>
  .product-page {
    font-family:
      'Hanken Grotesk',
      system-ui,
      -apple-system,
      BlinkMacSystemFont,
      'Segoe UI',
      sans-serif;
  }

  .product-heading {
    font-family: 'Archivo Narrow', 'Arial Narrow', Arial, sans-serif;
  }

  .product-image-enter-active,
  .product-image-leave-active {
    transition:
      opacity 180ms ease,
      transform 180ms ease;
  }

  .product-image-enter-from {
    opacity: 0;
    transform: scale(0.985);
  }

  .product-image-leave-to {
    opacity: 0;
    transform: scale(1.01);
  }
</style>
