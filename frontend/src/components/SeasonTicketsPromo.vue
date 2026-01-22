<template>
  <section class="w-full py-12">
    <div class="container mx-auto px-4">
      <div class="relative overflow-hidden rounded-2xl border border-white/10">
        <!-- image -->
        <img
          :src="imageSrc"
          :alt="title"
          class="w-full h-[full] sm:h-[360px] md:h-[620px] object-cover"
        />

        <!-- overlay -->
        <div
          class="absolute inset-0 bg-gradient-to-r from-[#071f36]/85 via-[#071f36]/45 to-transparent"
        ></div>

        <!-- optional badge -->
        <div
          v-if="badgeText"
          class="absolute left-4 top-4 sm:left-6 sm:top-6 inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-bold tracking-widest text-white backdrop-blur"
        >
          {{ badgeText }}
        </div>

        <!-- content -->
        <div class="absolute inset-0 flex items-end sm:items-center">
          <div class="p-5 sm:p-8 max-w-xl">
            <h3 class="text-white text-xl sm:text-3xl font-extrabold tracking-wide uppercase">
              {{ title }}
            </h3>

            <p v-if="subtitle" class="mt-2 text-white/80 text-sm sm:text-base">
              {{ subtitle }}
            </p>

            <div class="mt-5 flex flex-wrap gap-3">
              <a
                v-if="primaryHref"
                :href="primaryHref"
                class="inline-flex items-center justify-center rounded-xl bg-[#2aa2ff] px-5 py-3 text-white font-bold text-sm hover:brightness-105 transition"
              >
                {{ primaryText }} →
              </a>

              <a
                v-if="secondaryHref"
                :href="secondaryHref"
                class="inline-flex items-center justify-center rounded-xl bg-white/10 px-5 py-3 text-white font-bold text-sm hover:bg-white/15 transition"
              >
                {{ secondaryText }}
              </a>
            </div>
          </div>
        </div>

        <!-- subtle glow -->
        <div
          class="pointer-events-none absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-[#2aa2ff]/20 blur-3xl"
        ></div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
  withDefaults(
    defineProps<{
      imageSrc: string
      title: string
      subtitle?: string
      badgeText?: string

      primaryHref?: string
      primaryText?: string

      secondaryHref?: string
      secondaryText?: string
    }>(),
    {
      subtitle: '',
      badgeText: '',
      primaryHref: '',
      primaryText: 'Kupi sezonsku',
      secondaryHref: '',
      secondaryText: 'Kontakt',
    }
  )
</script>
