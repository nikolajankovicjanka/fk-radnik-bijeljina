<template>
    <section class="gi-fluid">

        <!-- TOP: Osnovne informacije (white bg, blue text) -->
        <div class="gi-section gi-section--light">
            <div class="gi-inner">
                <h2 class="gi-title">Osnovne informacije</h2>

                <div class="gi-block">
                    <h3 class="gi-subtitle">Adresa</h3>
                    <address class="gi-address">
                        <span>Balkanska BB</span>
                        <span>Gradski stadion Bijeljina</span>
                        <span>Bijeljina, 76300</span>
                        <span>Republika Srpska, BiH</span>
                    </address>
                </div>

                <ul class="gi-facts">
                    <li><strong>Nadimak:</strong> Plavo-bijeli</li>
                    <li><strong>Osnovan:</strong> 1945</li>
                    <li><strong>Kapacitet stadiona:</strong> 6.000</li>
                    <li><strong>Teren:</strong> 105 x 68 metara</li>
                </ul>
            </div>
        </div>
        <!-- MIDDLE: Kontakt (Radnik gradient, white text) -->
        <div class="gi-section gi-section--brand">
            <div class="gi-inner gi-contact">

                <!-- LEFT: tekst -->
                <div class="gi-contact__text">
                    <h2 class="gi-title gi-title--inverse">Kontakt detalji</h2>

                    <p class="gi-text gi-text--inverse">
                        Opšte informacije:<br>
                        <a href="tel:+38755201338"> +387 55 201 338</a>
                        <span><br>  (Pon–Pet, 09:00–17:00)</span>
                    </p>

                    <p class="gi-text gi-text--inverse">
                        Ulaznice:
                        <a href="tel:+38700000001"><br>+387 00 000 001</a>
                        <span><br>(Pon–Pet, 10:00–17:00)</span>
                    </p>

                    <p class="gi-text gi-text--inverse">
                        E-mail:
                        <a href="mailto:fkradnikbijeljina@gmail.com">fkradnikbijeljina@gmail.com</a>
                    </p>
                </div>

                <!-- RIGHT: logo -->
                <div class="gi-contact__logo">
                    <img
                            src="/logo/FK_Radnik_logo.png"
                            alt="FK Radnik Bijeljina"
                    />
                </div>

            </div>
        </div>

        <!-- BOTTOM: Ostalo (white bg, blue text) -->
        <div class="gi-section gi-section--light">
            <div class="gi-inner">
                <h2 class="gi-title">Dan utakmice – opšte informacije</h2>
                <p class="gi-text">
                    Informacije o ulazima, parkingu i pravilima ponašanja
                    dostupne su u vodiču za dan utakmice.
                </p>

                <h2 class="gi-title">Pravila ponašanja</h2>
                <p class="gi-text">
                    Pravila stadiona važe za sve posjetioce. Unos pirotehnike i
                    opasnih predmeta je zabranjen.
                </p>
            </div>
        </div>

    </section>
</template>

<script setup lang="ts">
// statički sadržaj – lako se kasnije prebaci na props ili API
</script>

<style scoped>
/* container-fluid */
.gi-fluid {
    width: 100%;
}

/* full width sections */
.gi-section {
    width: 100%;
    padding: clamp(36px, 4vw, 64px) 0;
}

/* inner container (content width) */
.gi-inner {
    width: min(1100px, calc(100% - 32px));
    margin: 0 auto;
}

/* light section */
.gi-section--light {
    background: #ffffff;
    color: #0a2d6b;
}

/* brand section (Radnik gradient) */
.gi-section--brand {
    background: linear-gradient(
            135deg,
            #0a2d6b 0%,
            #172573 55%,
            #3433cd 100%
    );
    color: #ffffff;
}

/* typography */
.gi-title {
    margin: 0 0 18px;
    font-size: clamp(22px, 2.6vw, 32px);
    font-weight: 900;
    letter-spacing: -0.02em;
}

.gi-title--inverse {
    color: #ffffff;
}

.gi-subtitle {
    margin: 0 0 10px;
    font-size: 16px;
    font-weight: 800;
}

.gi-address span {
    display: block;
    line-height: 1.8;
    font-style: normal;
    font-size: 15px;
}

.gi-facts {
    list-style: none;
    padding: 0;
    margin: 18px 0 0;
    display: grid;
    gap: 10px;
}

.gi-facts li {
    font-size: 15px;
    font-weight: 800;
}

.gi-text {
    margin: 0 0 12px;
    font-size: 15px;
    line-height: 1.75;
}

.gi-text--inverse {
    color: #ffffff;
}

.gi-text--inverse a {
    color: #ffffff;
    font-weight: 900;
    text-decoration: underline;
}

.gi-text a {
    color: #f6f6fa;
    font-weight: 900;
    text-decoration: none;
}

.gi-text a:hover {
    text-decoration: underline;
}

/* mobile */
@media (max-width: 520px) {
    .gi-text,
    .gi-address span,
    .gi-facts li {
        font-size: 14px;
    }
}

/* Kontakt sekcija layout */
.gi-contact {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    gap: clamp(24px, 4vw, 64px);
}

/* Logo wrapper */
.gi-contact__logo {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Logo image */
.gi-contact__logo img {
    width: clamp(120px, 16vw, 200px);
    height: auto;
    opacity: 0.9; /* blagi watermark feel */
    filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.35));
}

/* Na mobilu: logo ide ispod teksta */
@media (max-width: 768px) {
    .gi-contact {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .gi-contact__logo {
        margin-top: 24px;
    }

    .gi-contact__logo img {
        width: 140px;
    }
}


</style>
