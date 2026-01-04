<script setup lang="ts">
import { ref } from 'vue'

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
}
</script>

<template>
    <header class="w-full bg-gradient-to-r from-[#061A2F] via-[#275a8e] to-[#061A2F]">
        <nav class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 h-20 relative">
            <div class="flex items-center gap-3">
                <img
                        src="/FK_Radnik_logo.png"
                        alt="FK Radnik Bijeljina"
                        class="h-12 w-auto"
                />
                <span class="text-white font-semibold tracking-wide uppercase text-sm">
                    FK Radnik Bijeljina
                </span>
            </div>

            <!-- CENTER: Desktop Navigation -->
            <ul class="hidden lg:flex items-center gap-8 text-sm font-semibold uppercase">
                <li v-for="item in navItems" :key="item.label">
                    <RouterLink
                            :to="item.to"
                            class="text-white/80 hover:text-blue-400 transition"
                            active-class="text-blue-400"
                    >
                        {{ item.label }}
                    </RouterLink>
                </li>
            </ul>

            <!-- RIGHT: Desktop Actions -->
            <div class="hidden lg:flex items-center gap-4">
                <!-- Search -->
                <button class="text-white/80 hover:text-blue-400 transition">
                    🔍
                </button>

                <!-- Login -->
                <RouterLink
                        to="/login"
                        class="bg-blue-500 hover:bg-blue-600 transition text-white text-sm font-semibold px-4 py-2 rounded-md"
                >
                    Login / Register
                </RouterLink>
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
                                src="/FK_Radnik_logo.png"
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

                    <!-- Mobile Login Button -->
                    <div class="mt-8 px-4">
                        <RouterLink
                                to="/login"
                                @click="closeMobileMenu"
                                class="block w-full text-center bg-blue-500 hover:bg-blue-600
                                   transition text-white text-sm font-semibold
                                   px-4 py-3 rounded-md uppercase tracking-wider"
                        >
                            Login / Register
                        </RouterLink>
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

.router-link-active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    border-radius: 1px;
}

/* Mobile menu scroll if needed */
@media (max-height: 600px) {
    .fixed.h-screen {
        overflow-y: auto;
    }
}

/* Better hover effects */
.hover\:bg-blue-400:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
</style>