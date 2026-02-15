<script setup lang="ts">
import {computed, onBeforeUnmount, onMounted, ref} from "vue"
import {i18n, setLocale} from "@/i18n"
import type {SupportedLocale} from "@/translation"

type LangOption = {
    code: SupportedLocale
    label: string
    flag: string
}

const languages = [
    {code: "sr-Latn", label: "Srpski (latinica)", flag: "🇷🇸"},
    {code: "sr-Cyrl", label: "Srpski (ćirilica)", flag: "🇷🇸"},
    {code: "en", label: "English", flag: "🇬🇧"},
    {code: "fr", label: "Français", flag: "🇫🇷"},
    {code: "es", label: "Español", flag: "🇪🇸"},
    {code: "de", label: "Deutsch", flag: "🇩🇪"},
] as const satisfies ReadonlyArray<LangOption>

const FALLBACK_LANG: LangOption = languages[0] ?? {
    code: "en",
    label: "English",
    flag: "🇬🇧"
}

const isLangOpen = ref(false)
const isMobileMenuOpen = ref(false)
const isScrolled = ref(false)

const selectedLang = computed<LangOption>(() => {
    const code = i18n.global.locale.value as SupportedLocale
    return languages.find((l) => l.code === code) ?? FALLBACK_LANG
})

function toggleLang() {
    isLangOpen.value = !isLangOpen.value
}

function onClickOutside(e: MouseEvent) {
    const target = e.target as HTMLElement
    if (!target.closest(".lang-dropdown")) isLangOpen.value = false
}

function toggleMobileMenu() {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}

function closeMobileMenu() {
    isMobileMenuOpen.value = false
    isLangOpen.value = false
}

function selectLang(lang: LangOption) {
    setLocale(lang.code)
    isLangOpen.value = false
}

function onScroll() {
    isScrolled.value = window.scrollY > 8
}

onMounted(() => {
    window.addEventListener("pointerdown", onClickOutside)
    window.addEventListener("scroll", onScroll, {passive: true})
    onScroll()
})

onBeforeUnmount(() => {
    window.removeEventListener("pointerdown", onClickOutside)
    window.removeEventListener("scroll", onScroll)
})

const navItems = [
    {key: "home", to: "/"},
    {key: "firstTeam", to: "/first-team"},
    {key: "club", to: "/club"},
    {key: "news", to: "/news"},
    {key: "matches", to: "/fixtures"},
    {key: "youth", to: "/youth-team"},
    {key: "women", to: "/women-team"},
] as const
</script>

<template>
    <header :class="['nav-radnik', isScrolled && 'nav-radnik--scrolled']">
        <div class="nav-bg" aria-hidden="true"></div>

        <nav class="nav-inner max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 h-20 relative">
            <!-- LEFT: Brand -->

            <RouterLink
                    :to="{ name: 'Home' }"
                    class="flex items-center gap-3 cursor-pointer"
            >
                <img
                        src="/logo/FK_Radnik_logo.png"
                        alt="FK Radnik Bijeljina"
                        class="h-12 w-auto"
                />
                <span class="text-white/90 font-semibold tracking-wide uppercase text-sm">
    FK Radnik SoccerBet
  </span>
            </RouterLink>

            <!-- CENTER: Desktop Navigation -->
            <ul class="hidden lg:flex items-center gap-8 text-[13px] font-semibold uppercase">
                <li v-for="item in navItems" :key="item.to">
                    <RouterLink
                            v-if="item.to === '/'"
                            to="/"
                            class="nav-link"
                            active-class="nav-link--active"
                            exact-active-class="nav-link--active"
                    >
                        {{ $t(`nav.${item.key}`) }}
                    </RouterLink>

                    <RouterLink
                            v-else
                            :to="item.to"
                            class="nav-link"
                            active-class="nav-link--active"
                    >
                        {{ $t(`nav.${item.key}`) }}
                    </RouterLink>
                </li>
            </ul>

            <!-- Desktop language -->
            <div class="hidden lg:block relative lang-dropdown">
                <button
                        type="button"
                        @click="toggleLang"
                        class="lang-btn"
                        aria-haspopup="listbox"
                        :aria-expanded="isLangOpen"
                >
                    <span class="text-base">{{ selectedLang.flag }}</span>
                    <span class="hidden xl:inline">{{
                            selectedLang.label
                        }}</span>
                    <span class="xl:hidden">{{
                            selectedLang.code.toUpperCase()
                        }}</span>
                    <span class="opacity-80">▾</span>
                </button>

                <div v-show="isLangOpen" class="lang-menu" role="listbox">
                    <button
                            v-for="lang in languages"
                            :key="lang.code"
                            type="button"
                            @click="selectLang(lang)"
                            class="lang-item"
                            :class="selectedLang.code === lang.code ? 'is-active' : ''"
                    >
                        <span class="text-base">{{ lang.flag }}</span>
                        <span>{{ lang.label }}</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE: Burger -->
            <div class="flex lg:hidden items-center gap-4">
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
                        <path
                                v-if="!isMobileMenuOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                        />
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
        </nav>

        <!-- MOBILE overlay -->
        <div
                v-if="isMobileMenuOpen"
                @click="closeMobileMenu"
                class="fixed inset-0 bg-black/50 z-[9998] lg:hidden"
        ></div>

        <!-- MOBILE drawer -->
        <div
                :class="[
        'fixed top-0 right-0 h-screen w-64 sm:w-72',
        'transform transition-transform duration-300 ease-in-out',
        'lg:hidden shadow-2xl z-[9999] isolate',
        isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full',
      ]"
                class="mobile-drawer"
        >
            <div class="flex items-center justify-between p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <img src="/logo/FK_Radnik_logo.png"
                         alt="FK Radnik Bijeljina" class="h-10 w-auto"/>
                    <span class="text-white font-semibold text-sm"> FK Radnik </span>
                </div>
                <button @click="closeMobileMenu"
                        class="text-white/80 hover:text-white p-1">✕
                </button>
            </div>

            <div class="p-4">
                <ul class="space-y-1">
                    <li v-for="item in navItems" :key="item.to">
                        <RouterLink
                                :to="item.to"
                                @click="closeMobileMenu"
                                :class="[
                'block py-3 px-4 rounded-lg text-white/80',
                'hover:bg-white/10 hover:text-white transition',
                'text-sm font-medium uppercase tracking-wide',
              ]"
                                active-class="bg-white/10 text-blue-400"
                        >
                            {{ $t(`nav.${item.key}`) }}
                        </RouterLink>
                    </li>
                </ul>

                <div class="mt-6 relative lang-dropdown">
                    <button
                            type="button"
                            @pointerdown.stop="toggleLang"
                            class="lang-btn w-full justify-between"
                            aria-haspopup="listbox"
                            :aria-expanded="isLangOpen"
                    >
            <span class="flex items-center gap-2">
              <span class="text-base">{{ selectedLang.flag }}</span>
              <span class="text-white/90">{{ selectedLang.label }}</span>
            </span>
                        <span class="opacity-80 text-white">▾</span>
                    </button>

                    <div v-show="isLangOpen" class="lang-menu mt-2 w-full"
                         role="listbox">
                        <button
                                v-for="lang in languages"
                                :key="lang.code"
                                type="button"
                                @pointerdown.stop="selectLang(lang)"
                                class="lang-item"
                                :class="selectedLang.code === lang.code ? 'is-active' : ''"
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
    </header>
</template>

<style scoped>
</style>
