<script setup lang="ts">
  import { computed } from 'vue'
  import { useRoute } from 'vue-router'
  import { ChevronRight, ShoppingCart } from 'lucide-vue-next'

  import { useCartStore } from '@/stores/cart'

  const route = useRoute()
  const cartStore = useCartStore()

  const isShopRoute = computed(() => route.path.startsWith('/shop'))

  const shouldShow = computed(
    () => isShopRoute.value && !cartStore.isEmpty && route.name !== 'ShopCart'
  )

  const itemLabel = computed(() => {
    return cartStore.totalItems === 1 ? '1 artikal' : `${cartStore.totalItems} artikla`
  })
</script>

<template>
  <Transition name="floating-cart">
    <RouterLink
      v-if="shouldShow"
      :to="{ name: 'ShopCart' }"
      class="floating-cart group fixed z-[60]"
      aria-label="Otvori korpu"
    >
      <!-- MOBILE -->
      <div
        class="relative flex h-14 w-14 items-center justify-center rounded-full bg-[#031426] shadow-[0_8px_30px_rgba(3,20,38,0.28)] transition duration-300 active:scale-95 sm:hidden"
      >
        <ShoppingCart :size="22" :stroke-width="2.2" class="text-[#edc06a]" />

        <span
          class="absolute -right-1 -top-1 flex h-6 min-w-6 items-center justify-center rounded-full border-2 border-white bg-[#dc2626] px-1.5 text-[10px] font-black text-white shadow"
        >
          {{ cartStore.totalItems > 99 ? '99+' : cartStore.totalItems }}
        </span>
      </div>

      <!-- DESKTOP / TABLET -->
      <div
        class="hidden min-w-[190px] overflow-hidden rounded-xl border border-white/10 bg-[#031426] shadow-[0_12px_40px_rgba(3,20,38,0.25)] transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-[0_18px_50px_rgba(3,20,38,0.32)] sm:block"
      >
        <div class="flex items-center gap-3 px-4 py-3.5">
          <!-- ICON -->
          <div
            class="relative flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-white/10"
          >
            <ShoppingCart :size="18" :stroke-width="2.2" class="text-[#edc06a]" />

            <span
              class="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#dc2626] px-1 text-[9px] font-black text-white shadow"
            >
              {{ cartStore.totalItems > 99 ? '99+' : cartStore.totalItems }}
            </span>
          </div>

          <!-- TEXT -->
          <div class="min-w-0 flex-1">
            <div class="text-[10px] font-black uppercase tracking-[0.12em] text-white">Korpa</div>

            <div class="mt-0.5 text-[10px] font-medium text-white/55">
              {{ itemLabel }}
            </div>
          </div>

          <ChevronRight
            :size="16"
            class="text-white/35 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:text-[#edc06a]"
          />
        </div>

        <!-- GOLD BOTTOM LINE -->
        <div class="h-[2px] w-full bg-[#edc06a]" />
      </div>
    </RouterLink>
  </Transition>
</template>

<style scoped>
  .floating-cart {
    right: max(16px, env(safe-area-inset-right));
    bottom: max(16px, env(safe-area-inset-bottom));
  }

  @media (min-width: 640px) {
    .floating-cart {
      right: 24px;
      bottom: 24px;
    }
  }

  /*
 * Entrance / exit animation
 */
  .floating-cart-enter-active,
  .floating-cart-leave-active {
    transition:
      opacity 0.25s ease,
      transform 0.3s ease;
  }

  .floating-cart-enter-from,
  .floating-cart-leave-to {
    opacity: 0;
    transform: translateY(15px) scale(0.92);
  }

  .floating-cart-enter-to,
  .floating-cart-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
</style>
