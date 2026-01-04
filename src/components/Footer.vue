<script setup lang="ts">
const socialMedia = [
    {
        icon: 'facebook',
        label: 'Facebook',
        url: '#',
        svg: 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'
    },
    {
        icon: 'twitter',
        label: 'Twitter',
        url: '#',
        svg: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z'
    },
    {
        icon: 'instagram',
        label: 'Instagram',
        url: '#',
        svg: 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z M2 9h4v12H2z M4 2a2 2 0 100 4 2 2 0 000-4z'
    },
    {
        icon: 'youtube',
        label: 'YouTube',
        url: '#',
        svg: 'M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z M9.75 15.02V8.98l5.75 3.02-5.75 3.02z'
    }
]

const footerLinks = [
    { label: 'CONTACT US', to: '/contact' },
    { label: 'PRIVACY POLICY', to: '/privacy' },
    { label: 'TERMS & CONDITIONS', to: '/terms' },
    { label: 'SITE MAP', to: '/sitemap' },
]
</script>

<template>
    <footer class="bg-gradient-to-b from-[#061A2F] to-[#0f3760] text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-10 pb-10 border-b border-white/20">
                <div class="text-center lg:text-left">
                    <h3 class="text-lg font-bold uppercase tracking-wider mb-4 text-blue-300">CLUB CONTACT</h3>
                    <div class="space-y-2 text-white/80 text-sm">
                        <p>FK Radnik Bijeljina</p>
                        <p>Balkanska BB</p>
                        <p>Bijeljina, Republika Srpska</p>
                        <p class="mt-1">+387 55 244 400</p>
                        <p>info@fkradnik.com</p>
                    </div>
                </div>
                <div class="order-first lg:order-none">
                    <div class="flex flex-col items-center">
                        <img
                                src="/FK_Radnik_logo.png"
                                alt="FK Radnik Bijeljina"
                                class="h-32 w-auto mb-4"
                        />
                        <span class="text-white font-bold text-lg tracking-wider uppercase">
              FK RADNIK BIJELJINA
            </span>
                    </div>
                </div>
                <div class="flex justify-center lg:justify-end space-x-4">
                    <a
                            v-for="social in socialMedia"
                            :key="social.icon"
                            :href="social.url"
                            :aria-label="social.label"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="bg-white/10 hover:bg-blue-500 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 group"
                    >
                        <svg
                                class="w-5 h-5 fill-current text-white group-hover:text-white"
                                viewBox="0 0 24 24"
                        >
                            <path :d="social.svg" />
                        </svg>
                    </a>
                </div>

            </div>
            <div class="py-8">
                <div class="flex flex-wrap justify-center gap-8 md:gap-12">
                    <RouterLink
                            v-for="link in footerLinks"
                            :key="link.label"
                            :to="link.to"
                            class="text-white/70 hover:text-blue-400 uppercase text-sm font-semibold tracking-wider transition-colors duration-300"
                    >
                        {{ link.label }}
                    </RouterLink>
                </div>
            </div>
            <div class="pt-6 border-t border-white/10">
                <p class="text-center text-white/90 text-xs uppercase tracking-wider">
                    © 2024 FK RADNIK BIJELJINA. ALL RIGHTS RESERVED.
                </p>
            </div>

        </div>
    </footer>
</template>

<style scoped>
</style>