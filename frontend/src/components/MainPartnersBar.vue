<template>
    <section
            class="relative w-full"
            aria-label="Main Partners"
    >
        <div
                class="relative w-full overflow-hidden"
                style="background: linear-gradient(180deg, #3332c9 0%, #071f36 100%);"
        >
            <!-- height of the bar -->
            <div class="h-[70px] sm:h-[84px]">
                <div class="container mx-auto px-4 h-full">
                    <div class="h-full flex items-center justify-center gap-6 sm:gap-10">
                        <!-- label -->
                        <div class="flex items-center gap-4 sm:gap-6">
              <span class="text-white text-[10px] sm:text-[11px] font-extrabold tracking-[0.25em] uppercase">
                OFFICIAL PARTNERS
              </span>
                            <span class="hidden sm:block h-6 w-px bg-white/15"></span>
                        </div>

                        <!-- logos -->
                        <div class="flex items-center gap-8 sm:gap-12">
                            <img
                                    src="/sponsors/kelme-logo.png"
                                    alt="Kelme"
                                    class="h-7 sm:h-9 w-auto object-contain opacity-90 hover:opacity-100 transition"
                                    loading="lazy"
                            />
                            <img
                                    src="/sponsors/soccer-bet-logo.png"
                                    alt="SoccerBet"
                                    class="h-7 sm:h-9 w-auto object-contain opacity-90 hover:opacity-100 transition"
                                    loading="lazy"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- subtle top glow -->
            <div class="pointer-events-none absolute inset-x-0 -top-10 h-20 bg-white/10 blur-2xl"></div>
            <!-- subtle vignette -->
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/0 via-black/10 to-black/25"></div>
        </div>
    </section>
</template>
