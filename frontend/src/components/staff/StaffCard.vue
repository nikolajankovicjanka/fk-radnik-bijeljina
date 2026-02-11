<script setup lang="ts">
import {computed} from "vue"
import type {ComponentPublicInstance} from "vue"
import type {StaffMember} from "@/types/staff"

const props = defineProps<{
    member: StaffMember
    setCardEl?: (el: Element | ComponentPublicInstance | null) => void
}>()

const setCardElSafe = (el: Element | ComponentPublicInstance | null) => {
    props.setCardEl?.(el)
}

const PLACEHOLDER = "/players/placeholder.png"

function normalizePhotoPath(photo?: string | null) {
    if (!photo) return null
    if (photo.startsWith("http://") || photo.startsWith("https://")) return photo
    if (photo.startsWith("/storage/")) return photo
    if (photo.startsWith("staff/")) return "/storage/" + photo
    if (photo.startsWith("/")) return photo
    return "/storage/" + photo
}

function toThumbPath(urlOrPath: string) {
    if (urlOrPath.endsWith(".webp")) {
        return urlOrPath.replace(".webp", "_thumb.webp")
    }
    return urlOrPath
}

const imgSrc = computed(() => {
    // prefer thumb url ako postoji
    const thumbUrl = normalizePhotoPath(props.member.photo_thumb_url)
    if (thumbUrl) return thumbUrl

    // fallback: napravimo thumb iz photo ili photo_url
    const base = normalizePhotoPath(props.member.photo ?? props.member.photo_url ?? null)
    if (!base) return PLACEHOLDER
    return toThumbPath(base)
})

const imgSrcSet = computed(() => {
    // prefer full url kao 2x ako postoji
    const full = normalizePhotoPath(props.member.photo_url ?? props.member.photo ?? null)
    if (!full) return ""

    const thumb = normalizePhotoPath(props.member.photo_thumb_url) ?? toThumbPath(full)
    return `${thumb} 1x, ${full} 2x`
})

const roleLabel = computed(() => {
    const map: Record<string, string> = {
        head_coach: "Šef stručnog štaba",
        assistant_coach: "Pomoćni trener",
        goalkeeper_coach: "Trener golmana",
        fitness_coach: "Kondicioni trener",
        physio: "Fizioterapeut",
        analyst: "Analitičar",
        team_manager: "Team manager",
    }
    const r = props.member.role ?? ""
    return map[r] ?? (r || "Staff")
})
</script>

<template>
    <article
            :ref="setCardElSafe"
            class="group relative overflow-hidden rounded-2xl border border-gray-200 shadow-lg bg-white reveal-card"
    >
        <!-- IMAGE -->
        <img
                :src="imgSrc"
                :srcset="imgSrcSet || undefined"
                sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                :alt="member.name"
                width="450"
                height="600"
                loading="lazy"
                decoding="async"
                class="h-[360px] sm:h-[420px] w-full
                   object-cover object-top
                   transition duration-500
                   group-hover:scale-[1.03] group-hover:blur-[2px]"
                @error="(e) => { (e.target as HTMLImageElement).src = PLACEHOLDER }"
        />

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>

        <!-- Bottom name (default state) -->
        <div
                class="absolute inset-x-0 bottom-0 pb-4 px-4 text-center transition duration-300 group-hover:opacity-0"
        >
            <div class="relative inline-block">
                <span class="text-base sm:text-lg font-extrabold text-white drop-shadow">
                    {{ member.name }}
                </span>

                <span
                        class="absolute left-0 -bottom-1 h-[3px] w-full bg-[#3332c9]
                           origin-left scale-x-0
                           transition-transform duration-500 ease-out
                           group-hover:scale-x-100"
                />
            </div>
        </div>

        <!-- Hover content -->
        <div
                class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center
                   opacity-0 transition duration-300 group-hover:opacity-100"
        >
            <div class="absolute inset-0 bg-black/45"></div>

            <div class="relative text-center px-4">
                <div class="mt-2 text-xs sm:text-sm font-extrabold uppercase tracking-[0.25em] text-white/85">
                    {{ roleLabel }}
                </div>
            </div>

            <div class="relative inline-block mt-4">
                <span class="text-lg sm:text-xl font-extrabold text-white drop-shadow">
                    {{ member.name }}
                </span>

                <span
                        class="absolute left-0 -bottom-1 h-[3px] w-full bg-[#3332c9]
                           origin-left scale-x-0
                           transition-transform duration-500 ease-out
                           group-hover:scale-x-100"
                />
            </div>
        </div>
    </article>
</template>
