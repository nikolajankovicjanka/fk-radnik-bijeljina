<template>
    <div>
        <HeroSlider></HeroSlider>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import HeroSlider from "../components/hero/HeroSlider.vue";

export default defineComponent({
    name: 'Home',
    components: {HeroSlider}
})
</script>

<style scoped>
h1 {
    font-size: 2rem;
    font-weight: bold;
}

.font-radnik {
    font-size: 2rem;
    color: blue;
}
</style>
