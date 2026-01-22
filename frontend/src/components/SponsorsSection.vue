<template>
    <section class="sp" aria-label="Sponsors">
        <div class="sp-inner">
            <!-- Header -->
            <div class="sp-head">
                <h2 class="sp-title">Officijalni partneri</h2>
            </div>
            <div class="sp-card">
                <a
                        v-for="s in sponsors"
                        :key="s.name"
                        class="sp-item"
                        :href="s.url || '#'"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="s.name"
                        :title="s.name"
                >
                    <img :src="s.logo" :alt="s.name" class="sp-logo"
                         loading="lazy"/>
                </a>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
type Sponsor = { name: string; logo: string; url?: string }

const sponsors: Sponsor[] = [
    {name: 'SoccerBet', logo: '/sponsors/soccerbet-trim.png', url: '#'},
    {name: 'Pass', logo: '/sponsors/pass-logo.png', url: '#'},
    {name: 'autoputevi', logo: '/sponsors/autoputevi-h70.png', url: '#'},
    {name: 'SoccerBet', logo: '/sponsors/soccerbet-h70.png', url: '#'},
    {name: 'Kelme', logo: '/sponsors/kelme-trim.png', url: '#'},
]
</script>

<style scoped>
.sp {
    width: 100%;
    padding: 56px 0 64px;
    background: radial-gradient(900px 420px at 50% 20%, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0) 60%),
    linear-gradient(180deg, #3332c9 0%, #071f36 100%);
}

.sp-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 18px;
}

/* Header */
.sp-head {
    text-align: center;
    margin-bottom: 20px;
}

.sp-title {
    margin: 0;
    color: rgba(255, 255, 255, 0.92);
    font-weight: 300;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-size: clamp(1.4rem, 2.6vw, 2.2rem);
}

.sp-sub {
    margin: 10px 0 0;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.95rem;
}

/* White logo card */
.sp-card {
    background: rgba(255, 255, 255, 0.92);
    border-radius: 18px;
    padding: 26px 26px;

    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 22px;

    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28),
    0 1px 0 rgba(255, 255, 255, 0.55) inset;
    margin: 0 auto;
}

.sp-item {
    width: 100%;
    display: grid;
    place-items: center;
    padding: 6px 0;
    border-radius: 12px;
    text-decoration: none;
    transition: transform 160ms ease,
    opacity 160ms ease;
    opacity: 0.9;
}

.sp-item:hover {
    opacity: 1;
    transform: scale(1.03);
}

/* Logo sizing */
.sp-logo {
    height: 56px; /* vizuelno jače */
    width: auto;
    max-width: 260px;
    object-fit: contain;
}

/* Mobile */
@media (max-width: 560px) {
    .sp-card {
        padding: 18px 16px;
        gap: 18px;
    }

    .sp-logo {
        height: 44px;
        max-width: 200px;
    }
}
</style>
