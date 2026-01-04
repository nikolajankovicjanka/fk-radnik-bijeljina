<template>
    <div class="min-h-screen flex flex-col bg-[#041526]">
        <!-- Navbar - fiksan na vrhu -->
        <Navbar class="sticky top-0 z-50 flex-shrink-0" />

        <!-- Glavni sadržaj - zauzima preostali prostor -->
        <main class="flex-grow">
            <RouterView />
        </main>

        <!-- Footer - uvijek na dnu -->
        <Footer class="flex-shrink-0 mt-auto" />
    </div>
</template>

<script setup lang="ts">
import Navbar from '../components/NavBar.vue'
import Footer from '../components/Footer.vue'
</script>

<style>
/* OPCIONALNO: Globalni stilovi ako treba */
.router-view-wrapper {
    padding-top: 1rem; /* Dodajte ako želite spacing ispod navbar-a */
}

/* Za prazne stranice */
.empty-page-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 60vh;
    padding: 2rem;
    text-align: center;
}
</style>