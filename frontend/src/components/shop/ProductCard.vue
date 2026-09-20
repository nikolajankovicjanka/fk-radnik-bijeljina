<script setup lang="ts">
  import { computed } from 'vue'
  import { ShoppingCart } from 'lucide-vue-next'
  import type { ShopProduct } from '@/types/shop'
  import { getShopImageUrl } from '@/utils/shop'

  const props = defineProps<{
    product: ShopProduct
  }>()

  const availableQuantity = computed(() =>
    props.product.variants.reduce(
      (total, variant) => total + Math.max(0, variant.stock_quantity - variant.reserved_quantity),
      0
    )
  )

  const productImage = computed(() => getShopImageUrl(props.product.main_image))

  const currentPrice = computed(() => props.product.sale_price ?? props.product.price)

  const hasDiscount = computed(
    () =>
      props.product.sale_price !== null &&
      Number(props.product.sale_price) < Number(props.product.price)
  )

  function formatPrice(price: string) {
    return `${Number(price).toFixed(2).replace('.', ',')} KM`
  }
</script>

<template>
  <article
    class="product-card group relative overflow-hidden rounded-lg border border-[#DCEAF4] bg-white shadow-sm transition-all duration-300 hover:-translate-y-[2px] hover:border-[#c6dce9] hover:shadow-md"
  >
    <!-- IMAGE -->
    <RouterLink
      :to="{
        name: 'ShopProduct',
        params: { slug: product.slug },
      }"
      class="relative block aspect-[4/5] overflow-hidden bg-[#f7f9fa]"
    >
      <img
        v-if="productImage"
        :src="productImage"
        :alt="product.name.trim()"
        class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.05]"
      />

      <!-- Placeholder dok proizvod nema fotografiju -->
      <div
        v-else
        class="flex h-full w-full items-center justify-center bg-gradient-to-b from-[#f8fafb] to-[#edf3f7]"
      >
        <img
          src="/logo/FK_Radnik_logo.png"
          :alt="product.name.trim()"
          class="w-[38%] max-w-[105px] opacity-[0.12] grayscale"
        />
      </div>

      <!-- BADGES -->
      <div class="absolute left-3 top-3 z-10 flex flex-col items-start gap-1.5">
        <span
          v-if="product.is_new"
          class="rounded-sm bg-[#edc06a] px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.1em] text-[#031426] shadow-sm"
        >
          Novo
        </span>

        <span
          v-if="product.is_bestseller"
          class="rounded-sm bg-[#031426] px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.08em] text-white shadow-sm"
        >
          Bestseller
        </span>

        <span
          v-if="hasDiscount"
          class="rounded-sm bg-[#006faf] px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.08em] text-white shadow-sm"
        >
          Akcija
        </span>
      </div>

      <!-- OUT OF STOCK -->
      <div
        v-if="availableQuantity <= 0"
        class="absolute inset-x-0 bottom-0 bg-[#031426]/90 px-3 py-2 text-center text-[9px] font-black uppercase tracking-[0.12em] text-white backdrop-blur-sm"
      >
        Trenutno nije dostupno
      </div>
    </RouterLink>

    <!-- CONTENT -->
    <div class="p-4">
      <!-- Category -->
      <p class="mb-1.5 truncate text-[9px] font-bold uppercase tracking-[0.12em] text-[#8e9197]">
        {{ product.category.name }}
      </p>

      <!-- Name -->
      <RouterLink
        :to="{
          name: 'ShopProduct',
          params: { slug: product.slug },
        }"
        class="block"
      >
        <h3
          class="product-title line-clamp-2 min-h-[42px] text-[18px] font-bold uppercase leading-[1.15] tracking-[-0.01em] text-[#031426] transition-colors duration-200 group-hover:text-[#006faf]"
        >
          {{ product.name.trim() }}
        </h3>
      </RouterLink>

      <!-- Price / action -->
      <div class="mt-4 flex min-h-[38px] items-center justify-between gap-3">
        <div class="min-w-0">
          <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
            <span class="text-[17px] font-extrabold leading-none text-[#006faf]">
              {{ formatPrice(currentPrice) }}
            </span>

            <span v-if="hasDiscount" class="text-[11px] font-medium text-[#8e9197] line-through">
              {{ formatPrice(product.price) }}
            </span>
          </div>
        </div>

        <!--
          Za sada vodi na detalje proizvoda.
          Kada uvedemo Pinia cart, ovo dugme postaje pravi Add to cart.
        -->
        <RouterLink
          :to="{
            name: 'ShopProduct',
            params: { slug: product.slug },
          }"
          :aria-label="`Pogledaj ${product.name.trim()}`"
          class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-[#DCEAF4] text-[#006faf] transition-all duration-250 hover:bg-[#006faf] hover:text-white"
        >
          <ShoppingCart :size="16" :stroke-width="2.2" />
        </RouterLink>
      </div>
    </div>
  </article>
</template>

<style scoped>
  .product-card {
    font-family:
      'Hanken Grotesk',
      system-ui,
      -apple-system,
      BlinkMacSystemFont,
      'Segoe UI',
      sans-serif;
  }

  .product-title {
    font-family: 'Archivo Narrow', 'Arial Narrow', Arial, sans-serif;
  }
</style>
