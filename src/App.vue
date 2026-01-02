<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'App'
})
</script>

<template>
    <!-- Ovo je wrapper za cijeli sajt -->
    <router-view />
</template>

<style>
/* Global reset (može kasnije Tailwind override) */
body {
    margin: 0;
    font-family: system-ui, sans-serif;
}
</style>
