<script lang="ts">
  import { defineComponent } from 'vue'
  import BackToTopFootball from '@/components/BackToTopFootball.vue'

  export default defineComponent({
    name: 'App',
    components: { BackToTopFootball },
  })
</script>

<template>
  <router-view />
  <BackToTopFootball />
</template>

<style>
  body {
    margin: 0;
    font-family: system-ui, sans-serif;
  }
</style>
