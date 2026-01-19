<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

type LangOption = {
    code: string
    label: string
    flag: string
}

const languages: LangOption[] = [
    { code: 'sr-Latn', label: 'Srpski (latinica)', flag: '🇷🇸' },
    { code: 'sr-Cyrl', label: 'Srpski (ćirilica)', flag: '🇷🇸' },
    { code: 'en', label: 'English', flag: '🇬🇧' },
    { code: 'fr', label: 'Français', flag: '🇫🇷' },
    { code: 'es', label: 'Español', flag: '🇪🇸' },
    { code: 'de', label: 'Deutsch', flag: '🇩🇪' },
]

const isLangOpen = ref(false)
const selectedLang = ref<LangOption>(languages[0])

function toggleLang() {
    isLangOpen.value = !isLangOpen.value
}

function onClickOutside(e: MouseEvent) {
    const target = e.target as HTMLElement
    if (!target.closest('.lang-dropdown')) {
        isLangOpen.value = false
    }
}

onMounted(() => {
    const saved = localStorage.getItem('fk_lang')
    if (saved) {
        const found = languages.find(l => l.code === saved)
        if (found) selectedLang.value = found
    }

    // bitno: pointerdown hvata ranije od click
    window.addEventListener('pointerdown', onClickOutside)
})

onBeforeUnmount(() => {
    window.removeEventListener('pointerdown', onClickOutside)
})

const navItems = [
    { label: 'Home', to: '/' },
    { label: 'About', to: '/about' },
    { label: 'News', to: '/news' },
    { label: 'Matches', to: '/fixtures' },
    { label: 'Youth', to: '/youth-team' },
    { label: 'Women', to: '/women-team' },
]

const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}


const closeMobileMenu = () => {
    isMobileMenuOpen.value = false
    isLangOpen.value = false
}
</script>

<template>
    <header class="w-full bg-gradient-to-r from-[#061A2F] via-[#275a8e] to-[#061A2F]">
        <nav class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 h-20 relative">
            <div class="flex items-center gap-3">
                <img
                        src="/logo/FK_Radnik_logo.png"
                        alt="FK Radnik Bijeljina"
                        class="h-12 w-auto"
                />
                <span class="text-white font-semibold tracking-wide uppercase text-sm">
                    FK Radnik SoccerBet
                </span>
            </div>

            <!-- CENTER: Desktop Navigation -->
            <ul class="hidden lg:flex items-center gap-8 text-sm font-semibold uppercase">
                <li v-for="item in navItems" :key="item.label">

                    <!-- HOME: mora biti exact -->
                    <RouterLink
                            v-if="item.to === '/'"
                            to="/"
                            class="nav-link"
                            active-class="nav-link--active"
                            exact-active-class="nav-link--active"
                    >
                        {{ item.label }}
                    </RouterLink>

                    <!-- OSTALI LINKOVI -->
                    <RouterLink
                            v-else
                            :to="item.to"
                            class="nav-link"
                            active-class="nav-link--active"
                    >
                        {{ item.label }}
                    </RouterLink>

                </li>
            </ul>

            <div class="hidden lg:block relative lang-dropdown">
                <button
                        type="button"
                        @click="toggleLang"
                        class="flex items-center gap-2 bg-white/10 hover:bg-white/15 transition text-white text-sm font-semibold px-4 py-2 rounded-md border border-white/15"
                        aria-haspopup="listbox"
                        :aria-expanded="isLangOpen"
                >
                    <span class="text-base">{{ selectedLang.flag }}</span>
                    <span class="hidden xl:inline">{{ selectedLang.label }}</span>
                    <span class="xl:hidden">{{ selectedLang.code.toUpperCase() }}</span>
                    <span class="opacity-80">▾</span>
                </button>

                <div
                        v-show="isLangOpen"
                        class="absolute right-0 mt-2 w-56 rounded-xl bg-[#061A2F] border border-white/10 shadow-2xl overflow-hidden z-50"
                        role="listbox"
                >
                    <button
                            v-for="lang in languages"
                            :key="lang.code"
                            type="button"
                            @click="selectLang(lang)"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm text-white/85 hover:bg-white/10 transition text-left"
                            :class="selectedLang.code === lang.code ? 'bg-white/10 text-white' : ''"
                    >
                        <span class="text-base">{{ lang.flag }}</span>
                        <span>{{ lang.label }}</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE: Burger Button & Actions -->
            <div class="flex lg:hidden items-center gap-4">
                <!-- Search Button (Mobile) -->
                <button class="text-white/80 hover:text-blue-400 transition">
                    🔍
                </button>

                <!-- Burger Menu Button -->
                <button
                        @click="toggleMobileMenu"
                        class="text-white p-2 focus:outline-none"
                        aria-label="Toggle menu"
                >
                    <svg
                            class="w-6 h-6 transition-transform duration-300"
                            :class="{ 'rotate-90': isMobileMenuOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                    >
                        <!-- Burger Icon Lines -->
                        <path
                                v-if="!isMobileMenuOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                        />
                        <!-- Close Icon (X) -->
                        <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- MOBILE MENU OVERLAY -->
            <div
                    v-if="isMobileMenuOpen"
                    @click="closeMobileMenu"
                    class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            ></div>

            <!-- MOBILE MENU CONTENT -->
            <div
                    :class="[
                    'fixed top-0 right-0 h-screen w-64 sm:w-72',
                    'bg-gradient-to-b from-[#061A2F] to-[#0B2D4F]',
                    'transform transition-transform duration-300 ease-in-out z-50',
                    'lg:hidden shadow-2xl',
                    isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'
                ]"
            >
                <!-- Mobile Menu Header -->
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <img
                                src="/logo/FK_Radnik_logo.png"
                                alt="FK Radnik Bijeljina"
                                class="h-10 w-auto"
                        />
                        <span class="text-white font-semibold text-sm">
                            FK Radnik
                        </span>
                    </div>
                    <button
                            @click="closeMobileMenu"
                            class="text-white/80 hover:text-white p-1"
                    >
                        ✕
                    </button>
                </div>

                <!-- Mobile Navigation Links -->
                <div class="p-4">
                    <ul class="space-y-1">
                        <li v-for="item in navItems" :key="item.label">
                            <RouterLink
                                    :to="item.to"
                                    @click="closeMobileMenu"
                                    :class="[
                                    'block py-3 px-4 rounded-lg text-white/80',
                                    'hover:bg-white/10 hover:text-white transition',
                                    'text-sm font-medium uppercase tracking-wide'
                                ]"
                                    active-class="bg-white/10 text-blue-400"
                            >
                                {{ item.label }}
                            </RouterLink>
                        </li>
                    </ul>

                    <div class="mt-6 relative lang-dropdown">
                        <button
                                type="button"
                                @pointerdown.stop="toggleLang"
                                class="w-full flex items-center justify-between gap-2 bg-white/10 hover:bg-white/15 transition text-white text-sm font-semibold px-4 py-3 rounded-md border border-white/15"
                                aria-haspopup="listbox"
                                :aria-expanded="isLangOpen"
                        >
    <span class="flex items-center gap-2">
      <span class="text-base">{{ selectedLang.flag }}</span>
      <span>{{ selectedLang.label }}</span>
    </span>
                            <span class="opacity-80">▾</span>
                        </button>

                        <div
                                v-show="isLangOpen"
                                class="mt-2 w-full rounded-xl bg-[#061A2F] border border-white/10 shadow-2xl overflow-hidden z-50"
                                role="listbox"
                        >
                            <button
                                    v-for="lang in languages"
                                    :key="lang.code"
                                    type="button"
                                    @pointerdown.stop="selectLang(lang)"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-white/85 hover:bg-white/10 transition text-left"
                                    :class="selectedLang.code === lang.code ? 'bg-white/10 text-white' : ''"
                            >
                                <span class="text-base">{{ lang.flag }}</span>
                                <span>{{ lang.label }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-12 px-4 text-white/60 text-xs text-center">
                        <p>FK Radnik Bijeljina</p>
                        <p class="mt-1">© 2024 Sva prava zadržana</p>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</template>

<style scoped>
/* Smooth transitions for mobile menu */
.router-link-active {
    position: relative;
}

.nav-link {
    position: relative;
    display: inline-block;
    padding: 6px 0; /* malo prostora da linija ne "lijepi" */
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.06em;
    transition: color 200ms ease;
}

.nav-link:hover {
    color: #ffffff;
}

/* bijela linija */
.nav-link::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: -6px;          /* podešavaš visinu ispod teksta */
    width: 100%;
    height: 2px;
    background: #ffffff;
    border-radius: 2px;

    transform: translateX(-50%) scaleX(0);
    transform-origin: center;
    transition: transform 220ms ease;
}

/* hover: širi se iz centra */
.nav-link:hover::after {
    transform: translateX(-50%) scaleX(1);
}

/* active link: linija stalno vidljiva */
.nav-link--active {
    color: #ffffff;
}

.nav-link--active::after {
    transform: translateX(-50%) scaleX(1);
}
</style>