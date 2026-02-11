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

            <!-- 🔝 GENERAL SPONSOR -->
            <div class="flex justify-center mb-16">
                <a
                        :href="generalSponsor.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white rounded-3xl shadow-lg px-10 py-8 hover:shadow-xl transition"
                >
                    <img
                            :src="generalSponsor.logo"
                            :alt="generalSponsor.name"
                            class="h-20 md:h-24 object-contain"
                            loading="lazy"
                    />
                </a>
            </div>

            <!-- 🏛 INSTITUTIONAL -->
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
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8">
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

const generalSponsor: Sponsor = {
    id: 1,
    name: "Soccerbet",
    logo: "/sponsors/soccerbet-trim.png",
    url: "#",
};

const institutionalSponsors: Sponsor[] = [
    {id: 2, name: "Vlada RS", logo: "/sponsors/vladara_RS.png", url: "#"},
    {
        id: 3,
        name: "Predsjednik RS",
        logo: "/sponsors/logo-predsjednik.png",
        url: "#"
    },
    {
        id: 4,
        name: "Grad Bijeljina",
        logo: "/sponsors/grad_bijeljina.png",
        url: "#"
    },
];

const otherSponsors: Sponsor[] = [
    {id: 6, name: "Chicago", logo: "/sponsors/chicago-logo.png", url: "#"},
    {id: 7, name: "Elvaco", logo: "/sponsors/elvaco-logo.png", url: "#"},
    {id: 8, name: "ERS", logo: "/sponsors/ers-log.png", url: "#"},
    {id: 9, name: "Globus", logo: "/sponsors/globus_logo.png", url: "#"},
    {id: 10, name: "Juventa", logo: "/sponsors/juventa-logo.png", url: "#"},
    {id: 11, name: "Kelme", logo: "/sponsors/kelme-logo.png", url: "#"},
    {id: 12, name: "Jelen", logo: "/sponsors/logo-jelen.webp", url: "#"},
    {
        id: 13,
        name: "Naša Banka",
        logo: "/sponsors/logo-nasa-banka.png",
        url: "#"
    },
    {id: 14, name: "RITE", logo: "/sponsors/logo-rite.png", url: "#"},
    {
        id: 15,
        name: "Tehnika Inžinjering",
        logo: "/sponsors/logo-tehnika-inzinjering.png",
        url: "#"
    },
    {id: 16, name: "Infocomp", logo: "/sponsors/logo_infocomp.png", url: "#"},
    {
        id: 17,
        name: "Motel Milošević",
        logo: "/sponsors/motel-milosevic.png",
        url: "#"
    },
    {id: 18, name: "Pass", logo: "/sponsors/pass-logo.png", url: "#"},
    {id: 20, name: "Steco", logo: "/sponsors/steco-logo.png", url: "#"},
    {id: 21, name: "Vitinka", logo: "/sponsors/vitinka-logo.png", url: "#"},
];
</script>