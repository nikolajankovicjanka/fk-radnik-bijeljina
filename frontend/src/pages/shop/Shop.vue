<script setup lang="ts">
  import { computed, onMounted, ref } from 'vue'
  import { ChevronDown, ChevronUp, SlidersHorizontal, X } from 'lucide-vue-next'

  import ProductCard from '@/components/shop/ProductCard.vue'
  import { shopService } from '@/services/shopService'
  import type { ShopProduct } from '@/types/shop'

  type SortOption = 'newest' | 'price-asc' | 'price-desc'

  const products = ref<ShopProduct[]>([])
  const loading = ref(true)
  const error = ref('')

  const activeCategory = ref<string>('all')
  const selectedSizes = ref<string[]>([])
  const onlyAvailable = ref(false)
  const sortBy = ref<SortOption>('newest')

  const categoryFilterOpen = ref(true)
  const sizeFilterOpen = ref(true)
  const priceFilterOpen = ref(false)
  const availabilityFilterOpen = ref(false)

  const mobileFiltersOpen = ref(false)

  async function loadProducts() {
    loading.value = true
    error.value = ''

    try {
      const response = await shopService.getProducts()
      products.value = response.data
    } catch (e) {
      console.error(e)
      error.value = 'Trenutno nije moguće učitati proizvode.'
    } finally {
      loading.value = false
    }
  }

  onMounted(loadProducts)

  /* =========================================================
   HELPERS
   ========================================================= */

  function productAvailableQuantity(product: ShopProduct) {
    return product.variants.reduce((total, variant) => {
      return total + Math.max(0, variant.stock_quantity - variant.reserved_quantity)
    }, 0)
  }

  function productPrice(product: ShopProduct) {
    return Number(product.sale_price ?? product.price)
  }

  /* =========================================================
   CATEGORIES
   ========================================================= */

  const categories = computed(() => {
    const categoryMap = new Map<
      string,
      {
        name: string
        slug: string
        count: number
      }
    >()

    products.value.forEach(product => {
      const category = product.category

      if (!category) return

      const existing = categoryMap.get(category.slug)

      if (existing) {
        existing.count += 1
        return
      }

      categoryMap.set(category.slug, {
        name: category.name,
        slug: category.slug,
        count: 1,
      })
    })

    return Array.from(categoryMap.values())
  })

  function setCategory(slug: string) {
    activeCategory.value = slug
  }

  /* =========================================================
   SIZES
   ========================================================= */

  const availableSizes = computed(() => {
    const sizes = new Set<string>()

    products.value.forEach(product => {
      product.variants.forEach(variant => {
        if (variant.is_active) {
          sizes.add(variant.size)
        }
      })
    })

    return Array.from(sizes)
  })

  function toggleSize(size: string) {
    if (selectedSizes.value.includes(size)) {
      selectedSizes.value = selectedSizes.value.filter(item => item !== size)

      return
    }

    selectedSizes.value.push(size)
  }

  /* =========================================================
   FILTER + SORT
   ========================================================= */

  const filteredProducts = computed(() => {
    let result = [...products.value]

    if (activeCategory.value !== 'all') {
      result = result.filter(product => product.category?.slug === activeCategory.value)
    }

    if (selectedSizes.value.length) {
      result = result.filter(product =>
        product.variants.some(
          variant =>
            selectedSizes.value.includes(variant.size) &&
            variant.is_active &&
            variant.stock_quantity - variant.reserved_quantity > 0
        )
      )
    }

    if (onlyAvailable.value) {
      result = result.filter(product => productAvailableQuantity(product) > 0)
    }

    switch (sortBy.value) {
      case 'price-asc':
        result.sort((a, b) => productPrice(a) - productPrice(b))
        break

      case 'price-desc':
        result.sort((a, b) => productPrice(b) - productPrice(a))
        break

      case 'newest':
      default:
        result.sort((a, b) => {
          if (a.is_new !== b.is_new) {
            return Number(b.is_new) - Number(a.is_new)
          }

          return a.sort_order - b.sort_order
        })
        break
    }

    return result
  })

  const filtersActive = computed(() => {
    return activeCategory.value !== 'all' || selectedSizes.value.length > 0 || onlyAvailable.value
  })

  function clearFilters() {
    activeCategory.value = 'all'
    selectedSizes.value = []
    onlyAvailable.value = false
  }

  /* =========================================================
   MOBILE FILTERS
   ========================================================= */

  function openMobileFilters() {
    mobileFiltersOpen.value = true
    document.body.style.overflow = 'hidden'
  }

  function closeMobileFilters() {
    mobileFiltersOpen.value = false
    document.body.style.overflow = ''
  }
</script>

<template>
  <div class="shop-page min-h-screen bg-[#F4F7F9]">
    <!-- =====================================================
         HERO
         ===================================================== -->
    <section
      class="shop-hero relative flex h-[330px] w-full items-center justify-center overflow-hidden border-b border-[#44474c] bg-[#031426] sm:h-[360px] lg:h-[400px]"
    >
      <!-- Stadium -->
      <div class="absolute inset-0">
        <div
          class="absolute inset-0 bg-cover bg-center bg-no-repeat"
          style="background-image: url('/club/fk-radnik-stadion.jpg')"
        ></div>

        <div class="absolute inset-0 bg-[#031426]/65"></div>

        <div
          class="absolute inset-0 bg-gradient-to-t from-[#031426] via-[#031426]/35 to-[#031426]/40"
        ></div>

        <div
          class="absolute inset-0 bg-gradient-to-r from-[#031426]/55 via-transparent to-[#031426]/55"
        ></div>
      </div>

      <!-- Hero content -->
      <div class="relative z-10 mx-auto flex max-w-4xl flex-col items-center px-4 text-center">
        <span
          class="mb-5 inline-flex border border-[#edc06a]/40 bg-[#031426]/50 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.24em] text-[#edc06a] backdrop-blur-sm"
        >
          Official Store
        </span>

        <h1
          class="shop-heading text-[46px] font-black uppercase leading-none tracking-[-0.035em] text-white drop-shadow-xl sm:text-[60px] lg:text-[78px]"
        >
          FK Radnik Shop
        </h1>

        <p class="mt-4 text-sm font-medium text-[#DCEAF4] sm:text-base">
          Plavo-bijeli. Gdje god da si.
        </p>
      </div>
    </section>

    <!-- =====================================================
         CATEGORY BAR
         ===================================================== -->
    <div class="sticky top-20 z-30 border-b border-[#44474c] bg-[#313537]">
      <div class="no-scrollbar mx-auto max-w-7xl overflow-x-auto px-4 sm:px-6">
        <div class="flex min-w-max items-center gap-7 lg:gap-9">
          <button
            type="button"
            class="category-tab"
            :class="{
              'category-tab--active': activeCategory === 'all',
            }"
            @click="setCategory('all')"
          >
            Sve
          </button>

          <button
            v-for="category in categories"
            :key="category.slug"
            type="button"
            class="category-tab"
            :class="{
              'category-tab--active': activeCategory === category.slug,
            }"
            @click="setCategory(category.slug)"
          >
            {{ category.name }}
          </button>
        </div>
      </div>
    </div>

    <!-- =====================================================
         SHOP CONTENT
         ===================================================== -->
    <main class="mx-auto min-h-[600px] max-w-7xl px-4 py-8 sm:px-6 lg:py-10">
      <!-- Mobile controls -->
      <div class="mb-6 flex items-center justify-between gap-4 lg:hidden">
        <button
          type="button"
          class="flex items-center gap-2 rounded border border-[#DCEAF4] bg-white px-4 py-3 text-xs font-black uppercase tracking-[0.08em] text-[#072846]"
          @click="openMobileFilters"
        >
          <SlidersHorizontal :size="17" />

          Filteri

          <span
            v-if="filtersActive"
            class="flex h-5 min-w-5 items-center justify-center rounded-full bg-[#006faf] px-1 text-[10px] text-white"
          >
            {{
              selectedSizes.length + (activeCategory !== 'all' ? 1 : 0) + (onlyAvailable ? 1 : 0)
            }}
          </span>
        </button>

        <select
          v-model="sortBy"
          class="rounded border border-[#DCEAF4] bg-white px-3 py-3 text-xs font-bold text-[#072846] outline-none"
        >
          <option value="newest">Najnovije</option>

          <option value="price-asc">Cijena: rastuće</option>

          <option value="price-desc">Cijena: padajuće</option>
        </select>
      </div>

      <div class="flex items-start gap-8">
        <!-- =================================================
             DESKTOP FILTERS
             ================================================= -->
        <aside class="hidden w-[230px] flex-shrink-0 lg:block">
          <div class="sticky top-[148px] rounded-lg border border-[#DCEAF4] bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between border-b border-[#DCEAF4] pb-4">
              <h2 class="shop-heading text-xl font-black uppercase text-[#072846]">Filteri</h2>

              <button
                v-if="filtersActive"
                type="button"
                class="text-[10px] font-bold uppercase tracking-wider text-[#006faf] hover:underline"
                @click="clearFilters"
              >
                Očisti
              </button>
            </div>

            <!-- Category filter -->
            <div class="border-b border-[#DCEAF4] pb-5">
              <button
                type="button"
                class="flex w-full items-center justify-between text-left"
                @click="categoryFilterOpen = !categoryFilterOpen"
              >
                <span class="text-[11px] font-black uppercase tracking-[0.08em] text-[#072846]">
                  Kategorija
                </span>

                <ChevronUp v-if="categoryFilterOpen" :size="15" class="text-[#8e9197]" />

                <ChevronDown v-else :size="15" class="text-[#8e9197]" />
              </button>

              <div v-show="categoryFilterOpen" class="mt-4 space-y-2.5">
                <label class="filter-checkbox">
                  <input
                    type="radio"
                    name="shop-category"
                    value="all"
                    :checked="activeCategory === 'all'"
                    @change="setCategory('all')"
                  />

                  <span> Sve ({{ products.length }}) </span>
                </label>

                <label v-for="category in categories" :key="category.slug" class="filter-checkbox">
                  <input
                    type="radio"
                    name="shop-category"
                    :value="category.slug"
                    :checked="activeCategory === category.slug"
                    @change="setCategory(category.slug)"
                  />

                  <span>
                    {{ category.name }}
                    ({{ category.count }})
                  </span>
                </label>
              </div>
            </div>

            <!-- Size filter -->
            <div v-if="availableSizes.length" class="border-b border-[#DCEAF4] py-5">
              <button
                type="button"
                class="flex w-full items-center justify-between text-left"
                @click="sizeFilterOpen = !sizeFilterOpen"
              >
                <span class="text-[11px] font-black uppercase tracking-[0.08em] text-[#072846]">
                  Veličina
                </span>

                <ChevronUp v-if="sizeFilterOpen" :size="15" class="text-[#8e9197]" />

                <ChevronDown v-else :size="15" class="text-[#8e9197]" />
              </button>

              <div v-show="sizeFilterOpen" class="mt-4 flex flex-wrap gap-2">
                <button
                  v-for="size in availableSizes"
                  :key="size"
                  type="button"
                  class="flex h-9 min-w-9 items-center justify-center rounded border px-2 text-[11px] font-black transition"
                  :class="
                    selectedSizes.includes(size)
                      ? 'border-[#006faf] bg-[#006faf] text-white'
                      : 'border-[#DCEAF4] bg-white text-[#072846] hover:border-[#006faf]'
                  "
                  @click="toggleSize(size)"
                >
                  {{ size }}
                </button>
              </div>
            </div>

            <!-- Price -->
            <div class="border-b border-[#DCEAF4] py-5">
              <button
                type="button"
                class="flex w-full items-center justify-between text-left"
                @click="priceFilterOpen = !priceFilterOpen"
              >
                <span class="text-[11px] font-black uppercase tracking-[0.08em] text-[#072846]">
                  Cijena
                </span>

                <ChevronUp v-if="priceFilterOpen" :size="15" class="text-[#8e9197]" />

                <ChevronDown v-else :size="15" class="text-[#8e9197]" />
              </button>

              <div v-show="priceFilterOpen" class="mt-4 text-xs leading-5 text-[#8e9197]">
                Sortiranje po cijeni možeš odabrati iznad proizvoda.
              </div>
            </div>

            <!-- Availability -->
            <div class="pt-5">
              <button
                type="button"
                class="flex w-full items-center justify-between text-left"
                @click="availabilityFilterOpen = !availabilityFilterOpen"
              >
                <span class="text-[11px] font-black uppercase tracking-[0.08em] text-[#072846]">
                  Dostupnost
                </span>

                <ChevronUp v-if="availabilityFilterOpen" :size="15" class="text-[#8e9197]" />

                <ChevronDown v-else :size="15" class="text-[#8e9197]" />
              </button>

              <label v-show="availabilityFilterOpen" class="filter-checkbox mt-4">
                <input v-model="onlyAvailable" type="checkbox" />

                <span> Samo proizvodi na stanju </span>
              </label>
            </div>
          </div>
        </aside>

        <!-- =================================================
             PRODUCTS
             ================================================= -->
        <section class="min-w-0 flex-1">
          <!-- Toolbar -->
          <div
            class="mb-7 hidden items-center justify-between border-b border-[#DCEAF4] pb-4 lg:flex"
          >
            <p class="text-xs text-[#8e9197]">
              Prikazano

              <span class="font-bold text-[#072846]">
                {{ filteredProducts.length }}
              </span>

              {{ filteredProducts.length === 1 ? 'proizvod' : 'proizvoda' }}
            </p>

            <div class="flex items-center gap-3">
              <label
                for="shop-sort"
                class="text-[10px] font-black uppercase tracking-[0.08em] text-[#072846]"
              >
                Sortiraj po:
              </label>

              <select
                id="shop-sort"
                v-model="sortBy"
                class="min-w-[150px] rounded border border-[#DCEAF4] bg-white px-3 py-2 text-xs font-medium text-[#072846] outline-none transition focus:border-[#006faf]"
              >
                <option value="newest">Najnovije</option>

                <option value="price-asc">Cijena: rastuće</option>

                <option value="price-desc">Cijena: padajuće</option>
              </select>
            </div>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div
              v-for="index in 8"
              :key="index"
              class="overflow-hidden rounded-lg border border-[#DCEAF4] bg-white"
            >
              <div class="aspect-[4/5] animate-pulse bg-[#edf3f7]"></div>

              <div class="space-y-3 p-4">
                <div class="h-4 w-4/5 animate-pulse bg-slate-200"></div>

                <div class="h-4 w-20 animate-pulse bg-slate-200"></div>
              </div>
            </div>
          </div>

          <!-- Error -->
          <div
            v-else-if="error"
            class="rounded-lg border border-red-200 bg-white px-6 py-14 text-center"
          >
            <p class="font-semibold text-[#031426]">
              {{ error }}
            </p>

            <button
              type="button"
              class="mt-5 bg-[#031426] px-6 py-3 text-xs font-black uppercase tracking-wider text-white transition hover:bg-[#006faf]"
              @click="loadProducts"
            >
              Pokušaj ponovo
            </button>
          </div>

          <!-- Products -->
          <div
            v-else-if="filteredProducts.length"
            class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4"
          >
            <ProductCard v-for="product in filteredProducts" :key="product.id" :product="product" />
          </div>

          <!-- Empty -->
          <div v-else class="rounded-lg border border-[#DCEAF4] bg-white px-6 py-16 text-center">
            <p class="shop-heading text-xl font-black uppercase text-[#072846]">Nema proizvoda</p>

            <p class="mt-2 text-sm text-[#8e9197]">
              Nema proizvoda koji odgovaraju izabranim filterima.
            </p>

            <button
              v-if="filtersActive"
              type="button"
              class="mt-5 text-xs font-black uppercase tracking-wider text-[#006faf]"
              @click="clearFilters"
            >
              Očisti filtere
            </button>
          </div>
        </section>
      </div>
    </main>

    <!-- =====================================================
         MOBILE FILTER OVERLAY
         ===================================================== -->
    <div
      v-if="mobileFiltersOpen"
      class="fixed inset-0 z-[10000] bg-black/55 lg:hidden"
      @click.self="closeMobileFilters"
    >
      <div
        class="absolute bottom-0 left-0 right-0 max-h-[85vh] overflow-y-auto rounded-t-2xl bg-white"
      >
        <div
          class="sticky top-0 z-10 flex items-center justify-between border-b border-[#DCEAF4] bg-white px-5 py-4"
        >
          <h2 class="shop-heading text-xl font-black uppercase text-[#072846]">Filteri</h2>

          <button
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F7F9] text-[#072846]"
            @click="closeMobileFilters"
          >
            <X :size="18" />
          </button>
        </div>

        <div class="space-y-7 p-5">
          <!-- Category -->
          <div>
            <h3 class="mb-4 text-[11px] font-black uppercase tracking-[0.08em] text-[#072846]">
              Kategorija
            </h3>

            <div class="space-y-3">
              <label class="filter-checkbox">
                <input
                  type="radio"
                  name="mobile-shop-category"
                  :checked="activeCategory === 'all'"
                  @change="setCategory('all')"
                />

                <span> Sve ({{ products.length }}) </span>
              </label>

              <label v-for="category in categories" :key="category.slug" class="filter-checkbox">
                <input
                  type="radio"
                  name="mobile-shop-category"
                  :checked="activeCategory === category.slug"
                  @change="setCategory(category.slug)"
                />

                <span>
                  {{ category.name }}
                  ({{ category.count }})
                </span>
              </label>
            </div>
          </div>

          <!-- Sizes -->
          <div v-if="availableSizes.length" class="border-t border-[#DCEAF4] pt-6">
            <h3 class="mb-4 text-[11px] font-black uppercase tracking-[0.08em] text-[#072846]">
              Veličina
            </h3>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="size in availableSizes"
                :key="size"
                type="button"
                class="flex h-10 min-w-10 items-center justify-center rounded border px-3 text-xs font-black"
                :class="
                  selectedSizes.includes(size)
                    ? 'border-[#006faf] bg-[#006faf] text-white'
                    : 'border-[#DCEAF4] text-[#072846]'
                "
                @click="toggleSize(size)"
              >
                {{ size }}
              </button>
            </div>
          </div>

          <!-- Availability -->
          <div class="border-t border-[#DCEAF4] pt-6">
            <label class="filter-checkbox">
              <input v-model="onlyAvailable" type="checkbox" />

              <span> Samo proizvodi na stanju </span>
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 border-t border-[#DCEAF4] pt-5">
            <button
              type="button"
              class="flex-1 border border-[#DCEAF4] px-4 py-3 text-xs font-black uppercase tracking-wider text-[#072846]"
              @click="clearFilters"
            >
              Očisti
            </button>

            <button
              type="button"
              class="flex-1 bg-[#006faf] px-4 py-3 text-xs font-black uppercase tracking-wider text-white"
              @click="closeMobileFilters"
            >
              Prikaži
              {{ filteredProducts.length }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .shop-page {
    font-family:
      'Hanken Grotesk',
      system-ui,
      -apple-system,
      BlinkMacSystemFont,
      'Segoe UI',
      sans-serif;
  }

  .shop-heading {
    font-family: 'Archivo Narrow', 'Arial Narrow', Arial, sans-serif;
  }

  /* =========================================================
   HERO
   ========================================================= */

  .shop-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;

    background: linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);

    background-size: 80px 100%;
    opacity: 0.35;
  }

  /* =========================================================
   CATEGORY BAR
   ========================================================= */

  .category-tab {
    position: relative;

    padding: 1rem 0;

    color: rgba(255, 255, 255, 0.62);

    font-size: 10px;
    line-height: 1;
    font-weight: 900;
    letter-spacing: 0.07em;
    text-transform: uppercase;

    transition:
      color 200ms ease,
      opacity 200ms ease;
  }

  .category-tab:hover {
    color: #ffffff;
  }

  .category-tab::after {
    content: '';

    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;

    height: 2px;

    background: #edc06a;

    transform: scaleX(0);
    transform-origin: center;

    transition: transform 200ms ease;
  }

  .category-tab--active {
    color: #edc06a;
  }

  .category-tab--active::after {
    transform: scaleX(1);
  }

  /* =========================================================
   FILTERS
   ========================================================= */

  .filter-checkbox {
    display: flex;
    align-items: center;
    gap: 0.65rem;

    cursor: pointer;

    color: #8e9197;

    font-size: 12px;
    line-height: 1.4;
  }

  .filter-checkbox:hover {
    color: #072846;
  }

  .filter-checkbox input {
    width: 14px;
    height: 14px;

    cursor: pointer;

    accent-color: #006faf;
  }

  /* =========================================================
   SCROLLBAR
   ========================================================= */

  .no-scrollbar {
    scrollbar-width: none;
  }

  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
</style>
