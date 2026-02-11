<template>
    <section class="relative w-full" aria-label="Main Partners">
        <div class="relative w-full overflow-hidden bg-[#f7f7f4]">
            <div class="h-[72px] sm:h-[88px] relative">
                <div class="container mx-auto px-4 h-full">
                    <div class="h-full flex items-center justify-center gap-6 sm:gap-10">
                        <div class="flex items-center gap-4 sm:gap-6 shrink-0">
              <span class="text-black/70 text-[10px] sm:text-[11px] font-extrabold tracking-[0.25em] uppercase">
                OFFICIAL SPONSOR
              </span>
                            <span class="hidden sm:block h-6 w-px bg-black/10"></span>
                        </div>
                        <div class="flex items-center gap-8 sm:gap-12 max-w-full overflow-x-auto no-scrollbar px-1">
                            <a
                                    href="#"
                                    class="group shrink-0 rounded-xl px-2 py-2 transition"
                                    aria-label="SoccerBet"
                            >
                                <img
                                        src="/sponsors/soccerbet-h70.png"
                                        alt="SoccerBet"
                                        class="h-7 sm:h-9 w-auto object-contain opacity-85 transition duration-300
                         group-hover:opacity-100 group-hover:-translate-y-0.5
                         drop-shadow-none group-hover:drop-shadow-[0_10px_18px_rgba(255,255,255,0.9)]"
                                        loading="lazy"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
