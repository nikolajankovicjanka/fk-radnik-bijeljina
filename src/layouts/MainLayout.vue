<template>
    <div class="min-h-screen flex flex-col">
        <Navbar class="sticky top-0 z-50 flex-shrink-0" />
        <main class="flex-grow">
            <RouterView />
        </main>
        <Footer class="flex-shrink-0 mt-auto" />
    </div>
</template>

<script setup lang="ts">
import Navbar from '../components/NavBar.vue'
import Footer from '../components/Footer.vue'
</script>

<style>
.router-view-wrapper {
    padding-top: 1rem; /* Dodajte ako želite spacing ispod navbar-a */
}
.empty-page-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 60vh;
    padding: 2rem;
    text-align: center;
}
</style>