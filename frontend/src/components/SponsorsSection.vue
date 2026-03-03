<template>
    <section
            class="sp relative overflow-hidden py-20"
            aria-label="Sponsors"
    >
        <!-- Background -->
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(1200px_700px_at_20%_35%,rgba(0,0,0,0.03),transparent_60%),radial-gradient(900px_600px_at_85%_40%,rgba(0,0,0,0.04),transparent_55%),linear-gradient(180deg,#ffffff_0%,#fafafa_55%,#f5f5f5_100%)]"></div>

        <div class="sp-inner max-w-7xl mx-auto px-6">
            <div class="sp-head text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-black tracking-tight">
                    {{ $t('home.sponsors.sponsors') }}
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-16">
                <a
                        v-for="sponsor in institutionalSponsors"
                        :key="sponsor.id"
                        :href="sponsor.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6"
                >
                    <img
                            :src="sponsor.logo"
                            :alt="sponsor.name"
                            class="h-16 object-contain"
                            loading="lazy"
                    />
                </a>
            </div>

            <!-- 🤝 OTHERS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <a
                        v-for="sponsor in otherSponsors"
                        :key="sponsor.id"
                        :href="sponsor.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition p-5"
                >
                    <img
                            :src="sponsor.logo"
                            :alt="sponsor.name"
                            class="h-12 object-contain grayscale hover:grayscale-0 transition"
                            loading="lazy"
                    />
                </a>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">


interface Sponsor {
    id: number | string;
    name: string;
    logo: string;
    url: string;
}

const institutionalSponsors: Sponsor[] = [
    {
        id: 2,
        name: "Vlada RS",
        logo: "/sponsors/vladara_RS.png",
        url: "https://vladars.rs/sr-sp-cyrl/Pages/default.aspx"
    },
    {
        id: 3,
        name: "Predsjednik RS",
        logo: "/sponsors/logo-predsjednik.png",
        url: "https://www.predsjednikrs.rs/sr/"
    },
    {
        id: 4,
        name: "Grad Bijeljina",
        logo: "/sponsors/grad_bijeljina.png",
        url: "https://www.gradbijeljina.org/"
    },
];

const otherSponsors: Sponsor[] = [
    {
        id: 6,
        name: "Kelme",
        logo: "/sponsors/kelme-logo.png",
        url: "https://kelme.com/en?srsltid=ARcRdnr-1VLX5o5CaMJ_nkYwYM9qWUwf2MAtGZ1upXoD2ZZrMAuGvfy8"
    },
    {
        id: 7,
        name: "Elvaco",
        logo: "/sponsors/elvaco-logo.png",
        url: "https://elvacometpro.com/en/"
    },
    {id: 8, name: "ERS", logo: "/sponsors/ers-log.png", url: "https://ers.ba/"},
    {
        id: 9,
        name: "Globus",
        logo: "/sponsors/globus_logo.png",
        url: "https://globus.ba/"
    },
    {
        id: 10,
        name: "Juventa",
        logo: "/sponsors/juventa-logo.png",
        url: "https://www.juventasport.com/"
    },
    {
        id: 11,
        name: "Chicago",
        logo: "/sponsors/chicago-logo.png",
        url: "https://www.facebook.com/p/Caffe-bar-Chicago-100063635726687/"
    },
    {
        id: 12,
        name: "Jelen",
        logo: "/sponsors/logo-jelen.webp",
        url: "https://www.jelenpivo.com/"
    },
    {
        id: 13,
        name: "Naša Banka",
        logo: "/sponsors/logo-nasa-banka.png",
        url: "https://www.nasa-banka.com/"
    },
    {
        id: 14,
        name: "RITE",
        logo: "/sponsors/logo-rite.png",
        url: "https://riteugljevik.com/"
    },
    {
        id: 15,
        name: "Tehnika Inžinjering",
        logo: "/sponsors/logo-tehnika-inzinjering.png",
        url: "https://www.tehnikainz.com/"
    },
    {
        id: 16,
        name: "Infocomp",
        logo: "/sponsors/logo_infocomp.png",
        url: "https://infocomp.ba/articles"
    },
    {
        id: 17,
        name: "Motel Milošević",
        logo: "/sponsors/motel-milosevic.png",
        url: "https://motelmilosevic.com/"
    },
    {
        id: 18,
        name: "Pass",
        logo: "/sponsors/pass-logo.png",
        url: "https://pass.de/de/standorte.html"
    },
    {
        id: 20,
        name: "Steco",
        logo: "/sponsors/steco-logo.png",
        url: "https://www.stecocentar.com/?srsltid=AfmBOorerCAHIcLCJ0zFZBzmtLmb3lK-GZnxf5xUpBwzqOZF5MZPZkan"
    },
    {
        id: 21,
        name: "Vitinka",
        logo: "/sponsors/vitinka-logo.png",
        url: "https://www.vitinka.com/"
    },
    {
        id: 22,
        name: "Hajduk",
        logo: "/sponsors/hajduk-logo.png",
        url: ""
    },
];
</script>