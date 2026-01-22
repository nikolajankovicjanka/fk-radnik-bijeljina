<template>
  <section class="relative w-full" aria-label="Main Partners">
    <div
      class="relative w-full overflow-hidden"
      style="background: linear-gradient(180deg, #3332c9 0%, #7fb2e7 100%)"
    >
      <!-- Content row -->
      <div class="h-[70px] sm:h-[84px]">
        <div class="container mx-auto px-4 h-full">
          <div class="h-full flex items-center justify-center gap-6 sm:gap-10">
            <!-- Left label -->
            <div class="flex items-center gap-4 sm:gap-6">
              <span
                class="text-white/95 text-[10px] sm:text-[11px] font-extrabold tracking-[0.25em] uppercase"
              >
                OFFICIAL PARTNERS
              </span>
              <span class="hidden sm:block h-6 w-px bg-white/15"></span>
            </div>

            <!-- Logos (scrollable on very small screens) -->
            <div
              class="flex items-center gap-8 sm:gap-12 max-w-full overflow-x-auto no-scrollbar px-1"
            >
              <a href="#" class="group shrink-0" aria-label="Kelme">
                <img
                  src="/sponsors/kelme-trim.png"
                  alt="Kelme"
                  class="h-7 sm:h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition"
                  loading="lazy"
                />
              </a>

              <a href="#" class="group shrink-0" aria-label="SoccerBet">
                <img
                  src="/sponsors/soccerbet-h70.png"
                  alt="SoccerBet"
                  class="h-7 sm:h-9 w-auto object-contain opacity-85 group-hover:opacity-100 transition"
                  loading="lazy"
                />
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Subtle top glow -->
      <div class="pointer-events-none absolute inset-x-0 -top-10 h-20 bg-white/10 blur-2xl"></div>

      <!-- Subtle vignette -->
      <div
        class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/0 via-black/10 to-black/25"
      ></div>

      <!-- Hairline borders for a cleaner "bar" feel -->
      <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-white/15"></div>
      <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-black/10"></div>
    </div>
  </section>
</template>

<style scoped>
  /* Tailwind doesn't include "no-scrollbar" by default; add this (or put in global css) */
  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
</style>
